<?php
namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;

class StartCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'start';

    /**
     * @var string
     */
    protected $description = 'Start command';

    /**
     * @var string
     */
    protected $usage = '/start';

    /**
     * @var string
     */
    protected $version = '1.2.0';

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Main command execution
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        // If you use deep-linking, get the parameter like this:
        // $deep_linking_parameter = $this->getMessage()->getText(true);

        return $this->replyToChat(
            'Hi there!' . PHP_EOL .
            'Этот бот поможет учить английские слова!' . PHP_EOL .
            'Суть проста, с помощью команды /settime выбираете когда вам будут напоминать слова. Благодаря тому, что слова будут в течении дня мелькать у вас перед глазами вы сможете без особых проблем выучить их. Так же иногда бот может попросить вас написать перевод слова. Это поможет еще лучше запомнить их!' . PHP_EOL .
            'Type /help to see all commands!'
        );
    }
}