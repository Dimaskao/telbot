<?php

/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

/**
 * Generic message command
 */
class GenericmessageCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = Telegram::GENERIC_MESSAGE_COMMAND;

    /**
     * @var string
     */
    protected $description = 'Handle generic message';

    /**
     * @var string
     */
    protected $version = '1.2.0';

    /**
     * @var bool
     */
    protected $need_mysql = true;

    /**
     * Execution if MySQL is required but not available
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function executeNoDb(): ServerResponse
    {
        // Try to execute any deprecated system commands.
        if (self::$execute_deprecated && $deprecated_system_command_response = $this->executeDeprecatedSystemCommand()) {
            return $deprecated_system_command_response;
        }

        return Request::emptyResponse();
    }

    /**
     * Execute command
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        // Try to continue any active conversation.
        if ($active_conversation_response = $this->executeActiveConversation()) {
            return $active_conversation_response;
        }

        // Try to execute any deprecated system commands.
        if (self::$execute_deprecated && $deprecated_system_command_response = $this->executeDeprecatedSystemCommand()) {
            return $deprecated_system_command_response;
        }

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
        if (mb_strtolower($word) !== mb_strtolower($lastWord)) {
            $data['text'] = "????????????????, ???????????????????? ?????? ??????!";
            return Request::sendMessage($data);
        }

        
        $sql = "UPDATE user SET is_active = true, last_word = null WHERE id = $user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $data['text'] = "??????????????????!";
        
        return Request::sendMessage($data);
    }
}
