<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierApi\Model\Channel\Validator;

use Magento\Framework\Exception\ValidatorException;
use MSP\NotifierApi\Api\Data\ChannelInterface;

/**
 * Validate a channel object - SPI
 *
 * @api
 */
interface ValidateChannelInterface
{
    /**
     * Execute validation. Return true on success or trigger an exception on failure
     * @param ChannelInterface $channel
     * @return void
     * @throws ValidatorException;
     */
    public function execute(ChannelInterface $channel): void;
}
