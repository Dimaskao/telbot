<?php
require_once __DIR__ . "/vendor/autoload.php";
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\ServerResponse;

require "secret.php";//TODO: Возможно изменить на .env
require_once 'db.php';
$sql = 'SELECT * FROM words_to_learn';
$result = $pdo->query($sql);
// $data = [];
// while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
//     $data[$row['user_id']][$row['id']] = $row['word'];
// }
$data = [
    'chat_id' => 406235431,
    'text'    => 'sdsd',
];
try{
    Request::sendMessage($data);
}catch(Exception $e){
    echo $e->getMessage();
}
foreach ($data as $user => $words) {
    
}
//print_r($data);