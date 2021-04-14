<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Exception;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Conversation;

class AddCommand extends UserCommand
{
    protected $name = 'add';
    protected $description = 'Добавление новых слов в словарь изучения.';
    protected $usage = '/add слово - перевод; слово - перевод ...';
    protected $version = '2.0.0';

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

                    $data['text'] = "Напишите слово на английском";
                    $result = Request::sendMessage($data);
                    break;
                } 
                $notes['en_word'] = $text;
                $text = '';

            case 1:
                if ($text === '') {
                    $notes['state'] = 1;
                    $this->conversation->update();

                    $data['text'] = "Напишите перевод";
                    $result = Request::sendMessage($data);
                    break;
                }
                $notes['ru_word'] = $text;
            
            case 2:
                $this->conversation->update();
                unset($notes['state']);
                $this->SaveWords($notes, $user_id);
                $data['text'] = 'Слово "' . $notes['en_word'] . '" добавлено';
                $this->conversation->stop();
                $result = Request::sendMessage($data);
                break;
        }

        return $result;
    }

    private function SaveWords($notes, $user_id): void
    {
        $en_word = $notes['en_word'];
        $ru_word = $notes['ru_word'];
        $number_of_displays = 0;
        require_once "db.php";
        //Убрать уязвимость инекций
        $sql = "INSERT INTO words_to_learn (`user_id`, `en_word`, `ru_word`, `number_of_displays`) VALUES ($user_id, '$en_word', '$ru_word', $number_of_displays)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

    }
}