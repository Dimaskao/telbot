<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class HelpCommand extends UserCommand
{
    protected $name = 'help';                      
    protected $description = 'Команда для отображения списка команд'; 
    protected $usage = '/add';                    
    protected $version = '1.0.0';                  

    public function execute(): ServerResponse
    {
        $message = $this->getMessage();

        $chat_id = $message->getChat()->getId();

        $data = [
            'chat_id' => $chat_id,
            'text'    => '/help - получить список команд',
        ];

        return Request::sendMessage($data);
    }
}