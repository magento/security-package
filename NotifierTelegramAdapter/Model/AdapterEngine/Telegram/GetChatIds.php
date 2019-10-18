<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTelegramAdapter\Model\AdapterEngine\Telegram;

use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;

class GetChatIds
{
    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * GetChatIds constructor.
     * @param ClientRepository $clientRepository
     */
    public function __construct(
        ClientRepository $clientRepository
    ) {
        $this->clientRepository = $clientRepository;
    }

    /**
     * Get a telegram client by token
     * @param string $token
     * @return array
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function execute(string $token): array
    {
        $bot = $this->clientRepository->get($token);

        $updates = $bot->getUpdates();

        $res = [];
        foreach ($updates as $update) {
            $message = $update->getMessage();
            if ($message) {
                $chat = $message->getChat();
                $chatId = $chat->getId();

                if ($chat->getTitle()) {
                    $res[$chatId] = $chat->getTitle();
                } else {
                    $res[$chatId] = $chat->getUsername();
                }
            }
        }

        return $res;
    }
}
