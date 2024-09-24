<?php

namespace App\Services;

use GuzzleHttp\Client;

class TelegramService
{
    protected $botToken;
    protected $chatId;

    public function __construct()
    {
        // Set your bot token and chat ID
        $this->botToken = env('TELEGRAM_BOT_TOKEN'); // Store your bot token in the .env file
        $this->chatId = env('TELEGRAM_CHAT_ID');     // Store the chat ID or channel ID where messages should be sent
    }

    // Send a message and return the message ID
    public function sendMessage($message)
    {
        $client = new Client();

        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
        
        $response = $client->post($url, [
            'form_params' => [
                'chat_id' => $this->chatId,
                'text' => $message,
            ],
        ]);
       
        $responseBody = json_decode($response->getBody(), true);
       
        if (isset($responseBody['result']['message_id'])) {
            return $responseBody['result']['message_id']; // Return the message ID
        }

        return false;
    }

    // Edit a message by its message ID
    public function editMessage($messageId, $newMessage)
    {
        $client = new Client();

        $url = "https://api.telegram.org/bot{$this->botToken}/editMessageText";

        $response = $client->post($url, [
            'form_params' => [
                'chat_id' => $this->chatId,
                'message_id' => $messageId,
                'text' => $newMessage,
            ],
        ]);

        return $response->getStatusCode() === 200;
    }
}
