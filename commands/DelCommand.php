<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Exception;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;

class DelCommand extends UserCommand
{
    protected $name = 'del';
    protected $description = 'Удалеие слов';
    protected $usage = '/del слово на английском | Указывайте только одно слово!';
    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $user_id = $message->getFrom()->getId();
        $word = $message->getText(true);
        try{
            $this->DelWords($word, $user_id);
        }catch(Exception $e){
            return $this->replyToChat($e->getMessage());
        }
        
        return $this->replyToChat('Слово "' . $word . '" удалено');
    }

    private function DelWords($message, $user_id): void
    {
        if ($message == '') {
            throw new \Exception("Пожалуйста, укажите слово на английскойм которое хотите удалить");
        }
        $onlyWord = trim($message);

        require_once "db.php";
        $sql = "DELETE FROM `words_to_learn` WHERE `en_word` = '$onlyWord' AND `user_id` = $user_id";
        $result = $pdo->exec($sql);

        if ( !$result ) {
            throw new Exception('Слово не найдено');
        }

    }
}