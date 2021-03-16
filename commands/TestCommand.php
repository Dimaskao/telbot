<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class TestCommand extends UserCommand
{
    protected $name = 'help';
    protected $description = 'Добавления новых слов в словарь изучения';
    protected $usage = '/help';
    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();

        //$text = $message->getText();
        $data = [
            'chat_id' => $chat_id,
            'text'    => "test",
        ];

        return Request::sendMessage($data);
    }
}