<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplateApi\Model\DatabaseTemplate\Validator;

use Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;

class ValidateDatabaseTemplate implements ValidateDatabaseTemplateInterface
{
    /**
     * @var ValidateDatabaseTemplateInterface[]
     */
    private $validators;

    /**
     * DatabaseTemplateValidatorChain constructor.
     * @param array $validators
     */
    public function __construct(
        array $validators = []
    ) {
        $this->validators = $validators;

        foreach ($this->validators as $k => $validator) {
            if (!($validator instanceof ValidateDatabaseTemplateInterface)) {
                throw new \InvalidArgumentException(
                    'Validator %1 must implement ValidateDatabaseTemplateInterface',
                    $k
                );
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function execute(DatabaseTemplateInterface $template): bool
    {
        foreach ($this->validators as $validator) {
            $validator->execute($template);
        }

        return true;
    }
}
