<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierSlackAdapter\Model\AdapterEngine\Slack;

use Maknz\Slack\Client;

class ClientFactory
{
    /**
     * @param string $webhook
     * @param array $settings
     * @return Client
     */
    public function create(string $webhook, array $settings): Client
    {
        return new Client($webhook, $settings);
    }
}
