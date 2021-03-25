<?php


namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class ShowCommand extends UserCommand
{
    protected $name = 'show';
    protected $description = 'Ткстовая команда';
    protected $usage = '/show';
    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();

        require_once $_SERVER['DOCUMENT_ROOT'] . "db.php";
        $result = $pdo->query('SELECT `word` FROM words_to_learn');
        $text = '';
        while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
            $text .= ' | ' . $row['word'];
        }
        if(!$text){
            $text = "Вы еще не начали учить слова. Воспользуйтесь командой /add что бы добавить слова. Если больше одного слова, используйте ';' в качестве разделителя";
        }
        
        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
        ];

        return Request::sendMessage($data);
    }
}