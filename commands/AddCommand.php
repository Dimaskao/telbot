<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\DB;

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
        
        $text = $message->getText();
        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
        ];

        return Request::sendMessage($data);
    }

    private function SaveWords($message): void
    {
    $onlyWords = str_replace('/app', '', $message);
    $wordsList = explode(';', $onlyWords);
    foreach ($wordsList as $i => $word ) {
    $wordsList[$i] = trim($word);
    }
    return Request::sendMessage($onlyWords);
    }
}