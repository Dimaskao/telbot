<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Exception;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;

class AddCommand extends UserCommand
{
    protected $name = 'add';
    protected $description = 'Добавление новых слов в словарь изучения.';
    protected $usage = '/add слово - перевод; слово - перевод ...';
    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        $message = $this->getMessage();
        $user_id = $message->getFrom()->getId();
        $word = $message->getText(true);
        try{
            $this->SaveWords($word, $user_id);
        }catch(Exception $e){
            return $this->replyToChat($e->getMessage());
        }
        
        return $this->replyToChat('Слово "' . $word . '" добавлено');
    }

    private function SaveWords($message, $user_id): void
    {
        if ($message == '') {
            throw new \Exception("Пожалуйста, напишите хотя бы одно слово. Используйте команнду /add. Слова разделяйте ';'");
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