<?php
require_once __DIR__ . "/vendor/autoload.php";

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
require "secret.php";//TODO: Возможно изменить на .env
require_once 'db.php';
$sql = 'SELECT * FROM words_to_learn';
$result = $pdo->query($sql);
$data = [];
while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
    $data[$row['user_id']][$row['id']] = $row['word'];
}



$API_KEY  = $token;
$BOT_NAME = 'Learn english words';

$telegram = new Telegram($API_KEY, $BOT_NAME);

foreach($data as $user_id => $words){
    $otvet = [
        'chat_id' => $user_id,
        'text'    => $words[array_rand($words)],
    ];

    $result = Request::sendMessage($otvet);
}