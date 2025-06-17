<?php

use GuzzleHttp\Client;

$client = new Client();

$response = $client->post('https://accounts.zoho.com/oauth/v2/token', [
    'form_params' => [
        'client_id'     => '1000.IXHYLAMYBC8WN6CK9FSE672SL8MB7I',
        'client_secret' => 'fdba6ba70a41e54bd3008f961f26ad1dc0f99744fd',
        'redirect_uri'  => 'http://localhost/callback',
        'grant_type'    => 'authorization_code',
        'code'          => '5046',
    ]
]);

echo $response->getBody();
