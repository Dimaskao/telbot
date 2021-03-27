<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Exception;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class AddCommand extends UserCommand
{
    protected $name = 'add';
    protected $description = 'Добавления новых слов в словарь изучения';
    protected $usage = '/add';
    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $user_id = $message->getFrom()->getId();
        $text = $message->getText(true);
        try{
            $this->SaveWords($text, $user_id);
        }catch(Exception $e){
            $text = $e->getMessage();
        }

        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
        ];
        
        return Request::sendMessage($data);
    }

    private function SaveWords($message, $user_id): void
    {
        if ($message == '') {
            throw new \Exception("Пожалкйста, напишите хотя бы одно слово. Используйте команнду /add. Слова разделяйте ';'");
        }
        require_once "db.php";
        //Убрать уязвимость инекций
        $wordsList = explode(';', $message);
        foreach ($wordsList as $k => $word ) {
            $word = trim($word);
            $sql = "INSERT INTO words_to_learn (`user_id`, `word`) VALUES ($user_id, '$word')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
        }

    }
}