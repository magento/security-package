<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Model\DatabaseTemplate\Validator;

use Magento\Framework\Exception\ValidatorException;
use Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;
use Magento\NotifierTemplateApi\Model\DatabaseTemplate\Validator\ValidateDatabaseTemplateInterface;

/**
 * @inheritdoc
 */
class ValidateName implements ValidateDatabaseTemplateInterface
{
    /**
     * @inheritdoc
     */
    public function execute(DatabaseTemplateInterface $template): bool
    {
        if (!trim($template->getName())) {
            throw new ValidatorException(__('Template name is required'));
        }

        return true;
    }
}
