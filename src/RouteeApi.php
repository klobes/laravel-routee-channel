<?php

namespace NotificationChannels\Routee;

use GuzzleHttp\Client;
use NotificationChannels\Routee\Exceptions\CouldNotSendNotification;

class RouteeApi
{

    private $appID;
    private $secret;
    private $from;
    private $endpoint;
    private $authToken;
    private $lastAuthTokenCreateTime;

    private $client;

    public function __construct($appID, $secret, $from)
    {
        $this->appID = $appID;
        $this->secret = $secret;
        $this->from = $from;
        $this->endpoint = "https://connect.routee.net/";
        $this->client = new Client([
            'timeout' => 10,
        ]);
    }

    public function refreshToken()
    {
        $authClient = new \GuzzleHttp\Client();
        try {
            $response = $authClient->request(
                "POST",
                "https://auth.routee.net/oauth/token",
                [
                    "form_params" => [
                        "grant_type" => "client_credentials",
                    ],
                    "headers" => [
                        "authorization" => "Basic " . base64_encode($this->appID . ":" . $this->secret),
                    ]
                ]
            );
        } catch (\GuzzleHttp\Exception\ClientException $e) {

            $response = $e->getResponse();
            $body = $response->getBody()->getContents();
            if($body) {
                throw CouldNotSendNotification::serviceRespondedWithAnError(json_decode($body)->message);
            }
        }

        $this->authToken = json_decode($response->getBody())->access_token;
        $this->lastAuthTokenCreateTime = time();
    }

    private function checkToken()
    {
        if ($this->lastAuthTokenCreateTime + 60 * 60 < time()) {
            $this->refreshToken();
        }
    }

    public function getBalance()
    {
        $this->checkToken();

        $response = $this->client->request(
            "GET",
            $this->endpoint . "accounts/me/balance",
            [
                "headers" => [
                    "authorization" => "Bearer " . $this->authToken,
                ]
            ]
        );

        return $response->getBody();
    }

    public function getSmsPrice($country_iso)
    {
        $this->checkToken();

        $response = $this->client->request(
            "GET",
            $this->endpoint . "system/prices",
            [
                "headers" => [
                    "authorization" => "Bearer " . $this->authToken,
                ],
                "form_params" => [
                    "service" => "sms",
                ],
            ]
        );

        $data = json_decode($response->getBody())->sms;
        foreach ($data as $entry) {
            if (strtolower($entry->iso) == strtolower($country_iso)) {
                return $entry;
            }
        }

        return false;
    }

    public function sendSMS($to, $content)
    {
        $this->checkToken();

        // send sms
        $response = $this->client->request(
            "POST",
            $this->endpoint . "sms",
            [
                "headers" => [
                    "authorization" => "Bearer " . $this->authToken,
                    "content-type" => "application/json",
                ],
                "body" => json_encode([
                    "body" => $content,
                    "to" => $to,
                    "from" => $this->from,
                ])
            ]
        );

        $body = $response->getBody();
        $statusCode = $response->getStatusCode();
        if ($statusCode === 400) {
            /*
            400001009: You don't have enough balance to send the SMS.
            400005000:The sender id is invalid.
            400000000:
                Validation Error.
                A required field is missing.
                Invalid value of a field.
            */
            $code = json_decode($body)->code;
            throw CouldNotSendNotification::serviceRespondedWithAnErrorCode($code);

        } else if ($statusCode === 401){
            // Invalid access token, maybe has expired
            throw CouldNotSendNotification::unauthorized();
        } else if ($statusCode === 403){
            // Your application's access token does not have the right to use SMS service.
            throw CouldNotSendNotification::forbidden();
        }

    }
}
