<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\NotifierApi\Model;

use Magento\NotifierApi\Api\Data\MessageInterface;
use Magento\NotifierApi\Api\Data\MessageExtensionInterface;

/**
 * @inheritdoc
 */
class Message implements MessageInterface
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var array
     */
    private $params;

    /**
     * @var MessageExtensionInterface
     */
    private $extensionAttributes;

    /**
     * @param string|null $message
     * @param array|null $params
     * @param MessageExtensionInterface|null $extensionAttributes
     */
    public function __construct(
        string $message = null,
        array $params = null,
        MessageExtensionInterface $extensionAttributes = null
    ) {
        $this->message = $message;
        $this->params = $params;
        $this->extensionAttributes = $extensionAttributes;
    }

    /**
     * @inheritdoc
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @inheritdoc
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): ?MessageExtensionInterface
    {
        return $this->extensionAttributes;
    }
}
