<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEmailAdapter\Model\AdapterEngine\ParamsValidator;

use Magento\Framework\Exception\ValidatorException;
use Magento\NotifierApi\Model\AdapterEngine\ParamsValidatorInterface;

class IsEmail implements ParamsValidatorInterface
{
    /**
     * @var string
     */
    private $parameterName;

    /**
     * @param string $parameterName
     */
    public function __construct(
        string $parameterName
    ) {
        $this->parameterName = $parameterName;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $params): bool
    {
        if (isset($params[$this->parameterName]) &&
            !empty($params[$this->parameterName])
        ) {
            $emails = preg_split('/[\r\n\s,]+/', $params[$this->parameterName]);
            foreach ($emails as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                    throw new ValidatorException(__('One or more email format is not valid'));
                }
            }
        }

        return true;
    }
}
