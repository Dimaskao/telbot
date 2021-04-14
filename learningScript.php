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
    $words[$row['id']] = $row;
}

$data = [
    'chat_id' => $user_id,
    'text'    => '',
];


$API_KEY  = $token;
$BOT_NAME = 'Learn english words';

$telegram = new Telegram($API_KEY, $BOT_NAME);

$wordObj = $words[array_rand($words)];

if ($wordObj['number_of_displays'] > 10) {
    $lastWord = $wordObj['en_word'];
    $data['text'] = 'напишите перевод  **' . $wordObj['en_word'] . '**';
    $sql = "UPDATE user SET is_active = false, last_word = '$lastWord' WHERE id = $user_id";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute();
    if ( !$result ) {
        $data['text'] = 'ops, fail';
    }
    return Request::sendMessage($data);
}

$word = $wordObj['en_word'] . ' - ' . $wordObj['ru_word'];
$word_id = $wordObj['id'];
$sql = "UPDATE words_to_learn SET number_of_displays = number_of_displays + 1 WHERE id = $word_id";
$stmt = $pdo->prepare($sql);
$stmt->execute();

$data['text'] = $word;
return Request::sendMessage($data);