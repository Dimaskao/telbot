<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Exception;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class DelCommand extends UserCommand
{
    protected $name = 'del';
    protected $description = 'Удалеие слов';
    protected $usage = '/del';
    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $user_id = $message->getFrom()->getId();
        $text = $message->getText(true);
        try{
            $this->DelWords($text, $user_id);
        }catch(Exception $e){
            $text = $e->getMessage();
        }

        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
        ];
        
        return Request::sendMessage($data);
    }

    private function DelWords($message, $user_id): void
    {
        if ($message == '') {
            throw new \Exception("Пожалкйста, укажите слово которое хотите удалить");
        }
        $onlyWord = trim($message);

        require_once "db.php";
        $sql = "DELETE FROM `words_to_learn` WHERE `word` = '$onlyWord' AND `user_id` = $user_id";
        $result = $pdo->exec($sql);
        echo $result . "s";
        if ( !$result ) {
            throw new Exception('Слово не найдено');
        }

    }
}