<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierApi\Model\AdapterEngine;

use InvalidArgumentException;

class AdapterValidator implements AdapterValidatorInterface
{
    /**
     * @var MessageValidatorInterface[]
     */
    private $messageValidators;

    /**
     * @var ParamsValidatorInterface[]
     */
    private $paramsValidators;

    /**
     * @param MessageValidatorInterface[] $messageValidators
     * @param ParamsValidatorInterface[] $paramsValidators
     * @throws InvalidArgumentException
     */
    public function __construct(
        $messageValidators = [],
        $paramsValidators = []
    ) {
        $this->messageValidators = $messageValidators;
        $this->paramsValidators = $paramsValidators;

        foreach ($this->messageValidators as $k => $messageValidator) {
            if (!($messageValidator instanceof MessageValidatorInterface)) {
                throw new InvalidArgumentException('Message validator %1 must implement MessageValidatorInterface', $k);
            }
        }

        foreach ($this->paramsValidators as $k => $paramValidator) {
            if (!($paramValidator instanceof ParamsValidatorInterface)) {
                throw new InvalidArgumentException('Message validator %1 must implement ParamsValidatorInterface', $k);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function validateMessage(string $message): bool
    {
        foreach ($this->messageValidators as $messageValidator) {
            $messageValidator->execute($message);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function validateParams(array $params): bool
    {
        foreach ($this->paramsValidators as $paramValidator) {
            $paramValidator->execute($params);
        }

        return true;
    }
}
