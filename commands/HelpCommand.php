<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class HelpCommand extends UserCommand
{
    protected $name = 'help';                      
    protected $description = 'Команда для отображения списка команд'; 
    protected $usage = '/help';                    
    protected $version = '1.0.0';                  

    public function execute(): ServerResponse
    {
        $message = $this->getMessage();

        $chat_id = $message->getChat()->getId();
        $commands = Request::getMyCommands();
        $t = $commands->getResult();
        $text = '';
        foreach ($t as $com) {
            $text = ' ' . $com; 
        }
        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
        ];
        // /help - получить список команд \n/add - добавить новые слова в словарь изучения\n"
        return Request::sendMessage($data);
    }
}