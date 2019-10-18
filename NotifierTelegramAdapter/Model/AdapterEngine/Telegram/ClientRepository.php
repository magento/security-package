<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTelegramAdapter\Model\AdapterEngine\Telegram;

use TelegramBot\Api\BotApi;

class ClientRepository
{
    /**
     * @var BotApi[]
     */
    private $clients = [];

    /**
     * @var BotFactory
     */
    private $botFactory;

    /**
     * ClientRepository constructor.
     * @param BotFactory $botFactory
     */
    public function __construct(BotFactory $botFactory)
    {
        $this->botFactory = $botFactory;
    }

    /**
     * Get a telegram client by token
     * @param string $token
     * @return BotApi
     */
    public function get(string $token): BotApi
    {
        if (!isset($this->clients[$token])) {
            $this->clients[$token] = $this->botFactory->create($token);
        }

        return $this->clients[$token];
    }
}
