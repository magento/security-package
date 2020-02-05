<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\NotifierApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Notifier message interface.
 *
 * @api
 */
interface MessageInterface extends ExtensibleDataInterface
{
    /**
     * Get message text.
     *
     * @return string
     */
    public function getMessage(): string;

    /**
     * Get parameters array.
     *
     * @return array
     */
    public function getParams(): array;

    /**
     * Get existing extension attributes object.
     *
     * Used fully qualified namespaces in annotations for proper work of extension interface/class code generation.
     *
     * @return \Magento\NotifierApi\Api\Data\MessageExtensionInterface|null
     */
    public function getExtensionAttributes(): ?MessageExtensionInterface;
}
