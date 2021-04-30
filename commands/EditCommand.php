<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Exception;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Conversation;

class EditCommand extends UserCommand
{
    protected $name = 'edit';
    protected $description = 'Изменение слов';
    protected $usage = '/edit слово на английском | Указывайте только одно слово!';
    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        $result = Request::emptyResponse();

        $message = $this->getMessage();

        $chat    = $message->getChat();
        $user    = $message->getFrom();
        $text    = trim($message->getText(true));
        $chat_id = $chat->getId();
        $user_id = $user->getId();

        $data = [
            'chat_id'       => $chat_id,
            'text'          => '',
            'reply_markup'  => Keyboard::remove(['selective' => true]),
        ];
        
        if ($chat->isGroupChat() || $chat->isSuperGroup()) {
            $data['reply_markup'] = Keyboard::forceReply(['selective' => true]);
        }

        $this->conversation = new Conversation($user_id, $chat_id, $this->getName());

        $notes = &$this->conversation->notes;
        !is_array($notes) && $notes = [];

        $state = $notes['state'] ?? 0;

        switch($state){
            case 0:
                if ($text === '') {
                    $notes['state'] = 0;
                    $this->conversation->update();

                    $data['text'] = "Какое слово(английское) вы хотите изменить?";
                    $result = Request::sendMessage($data);
                    break;
                } 

                try{
                    $this->isWordExist($text, $user_id);
                }catch(Exception $e){
                    $data['text'] = $e->getMessage();
                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['word_to_edit'] = $text;
                $text = '';
            case 1:
                if ($text === '') {
                    $notes['state'] = 1;
                    $this->conversation->update();

                    $data['text'] = "Напишите слово на английском";
                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['en_word'] = $text;
                $text = '';
            case 2:
                if ($text === '') {
                    $notes['state'] = 2;
                    $this->conversation->update();

                    $data['text'] = "Напишите перевод";
                    $result = Request::sendMessage($data);
                    break;
                }
                $notes['ru_word'] = $text;
            
            case 3:
                $this->conversation->update();
                unset($notes['state']);
                $this->EditWords($notes, $user_id);
                $data['text'] = 'Слово "' . $notes['en_word'] . '" добавлено';
                $this->conversation->stop();
                $result = Request::sendMessage($data);
                break;
        }

        return $result;
    }

    private function isWordExist($en_word, $user_id): void
    {
        require_once "db.php";
        $result = $pdo->query("SELECT en_word FROM words_to_learn WHERE `user_id` = $user_id AND `en_word` = '$en_word'");
        if (!$result->fetch(\PDO::FETCH_ASSOC)) {
            throw new \Exception('Слово не найдено');
        }
    }

    private function EditWords($notes, $user_id): void
    {
        $en_word = $notes['en_word'];
        $ru_word = $notes['ru_word'];
        $word_to_edit = $notes['word_to_edit'];
        require_once "db.php";
        //Убрать уязвимость инекций
        $sql = "UPDATE words_to_learn SET en_word = '$en_word', ru_word = '$ru_word' WHERE user_id = $user_id AND en_word = '$word_to_edit'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

    }
}