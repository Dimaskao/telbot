<?php
require_once __DIR__ . "/vendor/autoload.php";

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
require "secret.php";//TODO: Возможно изменить на .env
require_once 'db.php';
$user_id = $argv[1];
$sql = "SELECT * FROM words_to_learn WHERE `user_id` = $user_id";
$result = $pdo->query($sql);
$words = [];
while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
    $words[$row['id']] = $row['word'];
}



$API_KEY  = $token;
$BOT_NAME = 'Learn english words';

$telegram = new Telegram($API_KEY, $BOT_NAME);


$data = [
    'chat_id' => $user_id,
    'text'    => $words[array_rand($words)],
];

    $result = Request::sendMessage($data);