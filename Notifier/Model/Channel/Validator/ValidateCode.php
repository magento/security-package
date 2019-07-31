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

class ValidateCode implements ValidateChannelInterface
{
    /**
     * @inheritDoc
     */
    public function execute(ChannelInterface $channel): void
    {
        if (empty(trim($channel->getCode()))) {
            throw new ValidatorException(__('No channel identifier is provided'));
        }

        if (!preg_match('/^[\w_]+$/', $channel->getCode())) {
            throw new ValidatorException(__('Invalid channel identifier: No special chars are allowed'));
        }
    }
}
