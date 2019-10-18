<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierApi\Model\AdapterEngine\ParamsValidator;

use Magento\Framework\Exception\ValidatorException;
use Magento\NotifierApi\Model\AdapterEngine\ParamsValidatorInterface;

/**
 * Sugar class to provide a simple check on a required parameter
 *
 * @api
 */
class Required implements ParamsValidatorInterface
{
    /**
     * @var array
     */
    private $requiredParams;

    /**
     * Required constructor.
     * @param array $requiredParams
     */
    public function __construct(
        array $requiredParams
    ) {
        $this->requiredParams = $requiredParams;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $params): bool
    {
        foreach ($this->requiredParams as $requiredParam) {
            if (!isset($params[$requiredParam]) ||
                ($params[$requiredParam] === null) ||
                (is_array($params[$requiredParam]) && empty($params[$requiredParam])) ||
                (is_string($params[$requiredParam]) && trim((string) $params[$requiredParam]) === '')
            ) {
                throw new ValidatorException(__('Parameter %1 cannot be empty', $requiredParam));
            }
        }

        return true;
    }
}
