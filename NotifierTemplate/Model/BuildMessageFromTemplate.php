<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Notifier\Model\BuildMessage;
use Magento\NotifierApi\Api\Data\MessageInterface;
use Magento\NotifierTemplateApi\Model\BuildMessageFromTemplateInterface;
use Magento\NotifierTemplateApi\Model\GetMessageTextInterface;

/**
 * @inheritdoc
 */
class BuildMessageFromTemplate implements BuildMessageFromTemplateInterface
{
    /**
     * @var GetMessageTextInterface
     */
    private $getMessageText;

    /**
     * @var BuildMessage
     */
    private $buildMessage;

    /**
     * @param GetMessageTextInterface $getMessageText
     * @param BuildMessage $buildMessage
     */
    public function __construct(
        GetMessageTextInterface $getMessageText,
        BuildMessage $buildMessage
    ) {
        $this->getMessageText = $getMessageText;
        $this->buildMessage = $buildMessage;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $channelCode, string $template, array $params = []): MessageInterface
    {
        $messageText = $this->getMessageText->execute($channelCode, $template, $params);

        return $this->buildMessage->execute($messageText, $params);
    }
}
