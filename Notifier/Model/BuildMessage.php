<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Notifier\Model;

use Magento\NotifierApi\Api\BuildMessageInterface;
use Magento\NotifierApi\Api\Data\MessageInterface;
use Magento\NotifierApi\Api\Data\MessageInterfaceFactory;

class BuildMessage implements BuildMessageInterface
{
    /**
     * @var MessageInterfaceFactory
     */
    private $messageFactory;

    public function __construct(
        MessageInterfaceFactory $messageFactory
    ) {
        $this->messageFactory = $messageFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $messageText, array $configParams): MessageInterface
    {
        return $this->messageFactory->create(
            ['message' => $messageText, 'params' => $configParams]
        );
    }
}
