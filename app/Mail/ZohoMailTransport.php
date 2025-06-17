<?php

namespace App\Mail;

use Closure;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\Exception\TransportException;
use Illuminate\Support\Facades\Cache;

class ZohoMailTransport extends AbstractTransport
{
    protected $client;
    protected $from;
    protected $clientId;
    protected $clientSecret;
    protected $refreshToken;
    protected $accountId;
    protected $zohoApiUrl;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30.0,
            'verify' => false
        ]);
        
        // Get configuration from environment
        $this->from = env('MAIL_FROM_ADDRESS');
        $this->clientId = env('ZOHO_CLIENT_ID');
        $this->clientSecret = env('ZOHO_CLIENT_SECRET');
        $this->refreshToken = env('ZOHO_REFRESH_TOKEN');
        $this->accountId = env('ZOHO_ACCOUNT_ID', '6657373000000002002'); // Using your account ID from the code
        $this->zohoApiUrl = env('ZOHO_API_URL', 'https://mail.zoho.eu');

        parent::__construct();
    }

    /**
     * Get a valid access token (refreshing if necessary)
     */
    protected function getAccessToken(): string
    {
        // Try to get token from cache first
        $accessToken = Cache::get('zoho_access_token');
        
        if (!$accessToken) {
            // Token not in cache or expired, refresh it
            $accessToken = $this->refreshAccessToken();
            
            // Store in cache for slightly less than expiry time (3600 seconds = 1 hour is typical)
            // Subtract 5 minutes to be safe
            Cache::put('zoho_access_token', $accessToken, 3300);
        }
        
        return $accessToken;
    }
    
    /**
     * Refresh the access token using the refresh token
     */
    protected function refreshAccessToken(): string
    {
        Log::info('Refreshing Zoho access token');
        
        try {
            $response = $this->client->post('https://accounts.zoho.eu/oauth/v2/token', [
                'form_params' => [
                    'refresh_token' => $this->refreshToken,
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'refresh_token'
                ]
            ]);
            
            $data = json_decode($response->getBody(), true);
            
            if (isset($data['access_token'])) {
                Log::info('Successfully refreshed Zoho access token');
                return $data['access_token'];
            } else {
                Log::error('Failed to refresh token: Invalid response', $data);
                throw new TransportException('Failed to refresh Zoho access token: Invalid response');
            }
        } catch (\Exception $e) {
            Log::error('Failed to refresh token', [
                'error' => $e->getMessage()
            ]);
            throw new TransportException('Failed to refresh Zoho access token: ' . $e->getMessage(), 0, $e);
        }
    }

    protected function doSend(SentMessage $message): void
    {
        $originalMessage = $message->getOriginalMessage();
        $envelope = $message->getEnvelope();

        // Validate recipients
        $recipients = $envelope->getRecipients();
        if (empty($recipients)) {
            throw new TransportException('No recipients specified');
        }

        // Extract and sanitize recipient emails
        $to = array_map(function($recipient) {
            // Extract email, remove any display names or special characters
            $email = $recipient->getAddress();
            
            // Validate and clean email
            $cleanEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
            
            if (!filter_var($cleanEmail, FILTER_VALIDATE_EMAIL)) {
                Log::warning("Invalid email address: $email");
                return null;
            }
            
            return $cleanEmail;
        }, $recipients);

        // Remove any null or invalid emails
        $to = array_filter($to);

        if (empty($to)) {
            throw new TransportException('No valid email recipients');
        }

        try {
            // Clean and extract content
            $content = $this->extractCleanContent($originalMessage);

            // Prepare payload
            $payload = [
                'fromAddress' => $this->from, 
                'toAddress' => implode(',', $to), // Comma-separated list of emails
                'subject' => $originalMessage->getSubject() ?? 'No Subject',
                'content' => $content,
                'mailFormat' => 'html'
            ];

            Log::info('Zoho Email Sending Attempt', [
                'from' => $this->from,
                'to' => $to,
                'subject' => $payload['subject'],
                'content_length' => strlen($content)
            ]);

            Log::debug('Zoho API Payload', $payload);

            // Get fresh access token
            $accessToken = $this->getAccessToken();
            
            $response = $this->client->post("{$this->zohoApiUrl}/api/accounts/{$this->accountId}/messages", [
                'headers' => [
                    'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
                    'Content-Type'  => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => $payload,
            ]);
            
            Log::info('Email sent successfully', [
                'status' => $response->getStatusCode(),
                'body' => (string)$response->getBody()
            ]);

        } catch (\Exception $e) {
            // Check if error is due to invalid token
            if (strpos($e->getMessage(), 'oauth') !== false || strpos($e->getMessage(), 'token') !== false) {
                Log::warning('Token issue detected, clearing cached token');
                Cache::forget('zoho_access_token');
                
                // Could retry the send once
            }
            
            Log::error('Email Sending Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new TransportException('Error sending email: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Extract clean content from the message
     */
    protected function extractCleanContent(RawMessage $message): string
    {
        try {
            // Handle different types of message bodies
            if ($message instanceof Email) {
                // Prefer HTML body
                $content = $message->getHtmlBody();
                
                // Fallback to plain text
                if (empty($content)) {
                    $content = $message->getTextBody();
                }
            } else {
                // Fallback method for generic messages
                $body = $message->getBody();
                $content = $body->toString();
            }

            // Remove MIME headers
            $content = preg_replace('/^Content-Type:.*\n?/mi', '', $content);
            $content = preg_replace('/^Content-Transfer-Encoding:.*\n?/mi', '', $content);
            
            // Trim whitespace
            $content = trim($content);

            // Fallback to HTML if empty
            if (empty($content)) {
                $content = '<p>Empty message body</p>';
            }

            return $content;
        } catch (\Exception $e) {
            Log::warning('Content extraction failed', [
                'error' => $e->getMessage()
            ]);
            return '<p>Unable to extract message content</p>';
        }
    }

    public function __toString(): string
    {
        return 'zoho';
    }
}