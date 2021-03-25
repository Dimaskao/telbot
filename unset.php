<?php
require_once __DIR__ . '/vendor/autoload.php';
require "secret.php";//TODO: Возможно изменить на .env

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($token, 'Learn english words');

    // Unset / delete the webhook
    $result = $telegram->deleteWebhook();

    echo $result->getDescription();
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e->getMessage();
}