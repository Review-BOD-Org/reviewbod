<?php
namespace App\Mail;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;

class ZohoTransport extends AbstractTransport
{
    protected $client;
    protected $accessToken;
    protected $accountId;

    public function __construct(LoggerInterface $logger = null)
    {
        parent::__construct($logger);
        $this->client = new Client();
        $this->accessToken = env('ZOHO_ACCESS_TOKEN');
        $this->accountId = env('ZOHO_ORG_ID');
// dd($this->accountId);
        if (!$this->accessToken || !$this->accountId) {
            throw new \RuntimeException("Zoho Mail API configuration missing: ZOHO_ACCESS_TOKEN or ZOHO_ACCOUNT_ID not set.");
        }
    }

    protected function doSend(SentMessage $message): void
    {
        /** @var Email $originalMessage */
        $originalMessage = $message->getOriginalMessage();

        // Extract required fields
        $from = $this->extractFirstAddress($originalMessage->getFrom());
        $to = $this->extractAddresses($originalMessage->getTo());
        $subject = $originalMessage->getSubject() ?? 'No Subject';
        $body = $this->extractMessageBody($originalMessage);

        if (!$from || empty($to)) {
            throw new \InvalidArgumentException('From address and recipients are required');
        }

        $apiUrl = "https://mail.zoho.com/api/accounts/{$this->accountId}/messages";

        try {
            $response = $this->client->post($apiUrl, [
                'headers' => [
                    'Authorization' => 'Zoho-oauthtoken ' . $this->accessToken,
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                ],
                'json' => [
                    'fromAddress' => $from,
                    'toAddress'   => implode(',', $to),
                    'subject'     => $subject,
                    'content'     => $body,
                    'mailFormat'  => 'html',
                ]
            ]);

            $responseBody = json_decode($response->getBody(), true);

            if ($response->getStatusCode() !== 200 || isset($responseBody['data']['errorCode'])) {
                throw new \Exception('Zoho Mail API Error: ' . json_encode($responseBody));
            }
        } catch (RequestException $e) {
            $errorResponse = $e->getResponse() ? (string) $e->getResponse()->getBody() : $e->getMessage();
            throw new \RuntimeException('Failed to send email: ' . $errorResponse);
        }
    }

    protected function extractFirstAddress(?array $addresses): ?string
    {
        if (empty($addresses)) {
            return null;
        }
        $firstAddress = reset($addresses);
        return $firstAddress ? $firstAddress->getAddress() : null;
    }

    protected function extractAddresses(?array $addresses): array
    {
        if (empty($addresses)) {
            return [];
        }

        return array_map(fn($address) => $address->getAddress(), $addresses);
    }

    protected function extractMessageBody(Email $message): string
    {
        return $message->getHtmlBody() ?: $message->getTextBody() ?: 'No message body';
    }

    public function __toString(): string
    {
        return 'zoho';
    }
}
