<?php
namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class GenericmessageCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'genericmessage';

    /**
     * @var string
     */
    protected $description = 'Handle generic message';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * Main command execution
     *
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {
        require_once 'db.php';
        
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $user_id = $message->getFrom()->getId();
        $data = [
            'chat_id' => $chat_id,
            'text' => '',
        ];

        $sql = "SELECT last_word FROM user WHERE `id` = $user_id";
        $result = $pdo->query($sql)->fetch(\PDO::FETCH_ASSOC);
        $lastWord = $result['last_word'];

        if ($lastWord === null) {
            return Request::emptyResponse();
        }

        
        $word = $message->getText(true);
        if ($word !== $lastWord) {
            $data['text'] = "Ошибочка, попробуйте еще раз!";
            return Request::sendMessage($data);
        }

        
        $sql = "UPDATE user SET is_active = true, last_word = null WHERE user_id = $user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $data['text'] = "Правильно!";
        
        return Request::sendMessage($data);
    }
}