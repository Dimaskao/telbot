<?php


namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class ShowCommand extends UserCommand
{
    protected $name = 'show';
    protected $description = 'Отображает изучаемые слова';
    protected $usage = '/show';
    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $user_id = $message->getFrom()->getId();
        require_once 'db.php';
        $result = $pdo->query("SELECT `word` FROM words_to_learn WHERE `user_id` = $user_id");
        $words = '';
        while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
            $words .= ' | ' . $row['word'];
        }
        if(!$words){
            return $this->replyToChat("Вы еще не начали учить слова. Воспользуйтесь командой /add что бы добавить слова. Если больше одного слова, используйте ';' в качестве разделителя");
        }
        
        return $this->replyToChat($words);
    }
}