<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\Notifier\Model\Channel\Validator;

use Magento\Framework\Exception\ValidatorException;
use MSP\NotifierApi\Api\Data\ChannelInterface;
use MSP\NotifierApi\Model\Channel\Validator\ValidateChannelInterface;

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
