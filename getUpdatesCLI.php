#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

$bot_api_key  = '1615051252:AAG8Vr68Ik5E5455Y2K2Tz2CgF0Rvwe4Nec';
$bot_username = 'Learn english words';
$USER_ID = 406235431;

$mysql_credentials = [
    'host'     => 'localhost',
    'user'     => 'root',
    'password' => '11111111',
    'database' => 'telbot',
];

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

    // Enable MySQL
    $telegram->enableMySql($mysql_credentials);

    // Handle telegram getUpdates request
    

    $telegram->addCommandsPath(__DIR__ . "/commands");
	$telegram->enableAdmin((int)$USER_ID);

    $server_response = $telegram->handleGetUpdates();

    if ($server_response->isOk()) {
        $update_count = count($server_response->getResult());
        echo date('Y-m-d H:i:s') . ' - Processed ' . $update_count . ' updates';
    } else {
        echo date('Y-m-d H:i:s') . ' - Failed to fetch updates' . PHP_EOL;
        echo $server_response->printError();
    }

} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // log telegram errors
    echo $e->getMessage();
}