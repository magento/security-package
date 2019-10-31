<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierApi\Model\Channel\Validator;

use InvalidArgumentException;
use Magento\NotifierApi\Api\Data\ChannelInterface;

class ValidateChannel implements ValidateChannelInterface
{
    /**
     * @var ValidateChannelInterface[]
     */
    private $validators;

    /**
     * @param array $validators
     * @throws InvalidArgumentException
     */
    public function __construct(
        array $validators = []
    ) {
        $this->validators = $validators;

        foreach ($this->validators as $k => $validator) {
            if (!($validator instanceof ValidateChannelInterface)) {
                throw new InvalidArgumentException('Validator ' . $k . ' must implement ValidateChannelInterface');
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function execute(ChannelInterface $channel): void
    {
        foreach ($this->validators as $validator) {
            $validator->execute($channel);
        }
    }
}
