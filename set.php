<?php
require __DIR__ . '/vendor/autoload.php';
require "secret.php";//TODO: Возможно изменить на .env
$bot_api_key  = $token;
$bot_username = 'Learn english words';
$hook_url     = 'https://www.telegbot.pp.ua/';

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

    // Set webhook
    $result = $telegram->setWebhook($hook_url);
    if ($result->isOk()) {
        echo $result->getDescription();
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // log telegram errors
    // echo $e->getMessage();
}