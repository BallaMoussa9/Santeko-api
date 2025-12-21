<?php

namespace App\Services;

use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client;

class BrevoMailer
{
    protected $api;

    public function __construct()
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', env('BREVO_API_KEY'));
        $this->api = new TransactionalEmailsApi(new Client(), $config);
    }

    public function send($toEmail, $subject, $htmlContent)
    {
        $sendSmtpEmail = new SendSmtpEmail([
            'to' => [['email' => $toEmail]],
            'sender' => [
                'email' => env('MAIL_FROM_ADDRESS'),
                'name'  => env('MAIL_FROM_NAME')
            ],
            'subject' => $subject,
            'htmlContent' => $htmlContent
        ]);

        try {
            $this->api->sendTransacEmail($sendSmtpEmail);
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
