<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTelegramAdapter\Command\Telegram;

use Magento\NotifierTelegramAdapter\Model\AdapterEngine\Telegram\GetChatIds\Proxy as GetChatIds;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Helper\Table;

class GetChatIdsCommand extends Command
{
    /**
     * @var GetChatIds
     */
    private $getChatIds;

    /**
     * @param GetChatIds $getChatIds
     */
    public function __construct(
        GetChatIds $getChatIds
    ) {
        parent::__construct();
        $this->getChatIds = $getChatIds;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('msp:notifier:telegram:chat_ids');
        $this->setDescription('Print chat IDs for a TelegramBot token');

        $this->addArgument('token', InputArgument::REQUIRED, 'BOT Token');

        parent::configure();
    }

    /**
     * @inheritdoc
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $token = $input->getArgument('token');
        $chatIds = $this->getChatIds->execute($token);

        // @codingStandardsIgnoreStart
        $table = new Table($output);
        // @codingStandardsIgnoreEnd
        $table->setHeaders(['Chat ID', 'Name']);

        $tableRows = [];
        foreach ($chatIds as $chatId => $title) {
            $tableRows[] = [$chatId, $title];
        }

        $table->setRows($tableRows);
        $table->render();
    }
}
