<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierTelegramAdapter\Model\AdapterEngine\Telegram;

use TelegramBot\Api\BotApi;

class BotFactory
{
    public function create(string $token): BotApi
    {
        return new BotApi($token);
    }
}
