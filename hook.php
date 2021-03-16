<?php
require_once __DIR__ . "/vendor/autoload.php";
require "secret.php";//TODO: Возможно изменить на .env


$API_KEY = $token; 
$USER_ID = 406235431;
$BOT_NAME = "Learn english words";
$mysql_credentials = [
   'host'     => 'localhost',
   'user'     => 'root',
   'password' => 'root',
   'database' => 'telbot',
];

use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;

try {
	$telegram = new Telegram($API_KEY, $BOT_NAME);
	$telegram->enableMySQL($mysql_credentials);

	$telegram->addCommandsPath(__DIR__ . "/commands");
	$telegram->enableAdmin((int)$USER_ID);

	TelegramLog::initUpdateLog($BOT_NAME . '_update.log');

	$telegram->handle();

} catch (Longman\TelegramBot\Exception\TelegramException $e) {
	var_dump($e);
}