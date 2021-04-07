<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Exception;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class SettimeCommand extends UserCommand
{
    protected $name = 'settime';
    protected $description = 'Устанавливает время напоминания слов';
    protected $usage = '/settime';
    protected $version = '1.0.0';
    protected $conversation;

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
            //'reply_markup'  => Keyboard::remove(['selective' => true]),
        ];

        if ($chat->isGroupChat() || $chat->isSuperGroup()) {
            $data['reply_markup'] = Keyboard::forceReply(['selective' => true]);
        }

        $this->conversation = new Conversation($user_id, $chat_id, $this->getName());

        $notes = &$this->conversation->notes;
        !is_array($notes) && $notes = [];

        $state = $notes['state'] ?? 0;
        if ( !isset($notes['days']) && !isset($notes['hours'])) {
            $notes['days']  = '*';
            $notes['hours'] = '10,20';
        }

        switch ($state) {
            case 0:
                if ($text === '') {
                    $notes['state'] = 0;
                    $this->conversation->update();
                    $keyboard = new Keyboard(
                        ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'],
                        ['Все дни']
                    );

                    $data['text'] = "В какие дни вы хотите получать сообщения?";
                    $data['reply_markup'] = $keyboard;
                    $result = Request::sendMessage($data);
                    break;
                }

                if ($text === 'Пн' || $text === 'Вт' || $text === 'Ср'|| $text === 'Чт' || $text === 'Пт' || $text ==='Сб' || $text === 'Вс') {
                    $notes['state'] = 0;

                    if ($notes['days'] === '*') {
                        $notes['days'] = $this->DayToNumber($text);
                    }else{
                        $notes['days'] .= ',' . $this->DayToNumber($text);
                    }
                    
                    $this->conversation->update();

                    $keyboard = new Keyboard(
                        ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'],
                        ['Сохранить']
                    );
                    $data['text'] = 'Выберете еще дни или нажмите сохранить';
                    $data['reply_markup'] = $keyboard;
                    $result = Request::sendMessage($data);
                    break;
                }


                $data['reply_markup'] = Keyboard::remove(['selective' => true]);
                $result = Request::sendMessage($data);

                $text  = '';

            case 1:
                if ($text === '') {
                    $notes['state'] = 1;
                    $this->conversation->update();

                    $data['text'] = "В какое время вы хотите получать сообщения? Напишите в формате ОТ,ДО. например 10,22 - от 10 часов до 22";
                    
                    $result = Request::sendMessage($data);

                    break;
                }
                // $this->replyToChat("fine");
                // exit;
                $hours = explode(',', $text);
                if ($hours[0] > 0 && $hours[0] <= 23 && $hours[1] > 0 && $hours[1] <= 23 && $hours[0] <= $hours[1]) {
                }else{
                    $data['text'] = "Формат введенных данных не правильный";
                    $result = Request::sendMessage($data);
                    break;
                }
                $notes['hours'] = $text;
                $text = '';
            case 2:
                $this->conversation->update();
                unset($notes['state']);
                
                $data['text'] = $notes['days'] . ' ' . $notes['hours'];
                $this->conversation->stop();
                $result = Request::sendMessage($data);
                break;
                
    
        }

        return $result;
    }

    private function DayToNumber(string $day): int
    {
        switch ($day) {
            case 'Пн':
                $numer_of_day = 1;
                break;
            case 'Вт':
                $numer_of_day = 2;
                break;
            case 'Ср':
                $numer_of_day = 3;
                break;
            case 'Чт':
                $numer_of_day = 4;
                break;
            case 'Пт':
                $numer_of_day = 5;
                break;
            case 'Сб':
                $numer_of_day = 6;
                break;
            case 'Вс':
                $numer_of_day = 7;
                break;
        }

        return $numer_of_day;
    }

}