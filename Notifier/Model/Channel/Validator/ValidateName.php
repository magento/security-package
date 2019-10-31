<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Notifier\Model\Channel\Validator;

use Magento\Framework\Exception\ValidatorException;
use Magento\NotifierApi\Api\Data\ChannelInterface;
use Magento\NotifierApi\Model\Channel\Validator\ValidateChannelInterface;

class ValidateName implements ValidateChannelInterface
{
    /**
     * @inheritDoc
     */
    public function execute(ChannelInterface $channel): void
    {
        if (!trim($channel->getName())) {
            throw new ValidatorException(__('Channel name is required'));
        }
    }
}
