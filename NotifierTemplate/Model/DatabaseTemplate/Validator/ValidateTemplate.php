<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Model\DatabaseTemplate\Validator;

use Magento\Framework\Exception\ValidatorException;
use Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;
use Magento\NotifierTemplateApi\Model\DatabaseTemplate\Validator\ValidateDatabaseTemplateInterface;

class ValidateTemplate implements ValidateDatabaseTemplateInterface
{
    /**
     * @inheritDoc
     */
    public function execute(DatabaseTemplateInterface $template): bool
    {
        if (!trim($template->getTemplate())) {
            throw new ValidatorException(__('Template is empty'));
        }

        return true;
    }
}
