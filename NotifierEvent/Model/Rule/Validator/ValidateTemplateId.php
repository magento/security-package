<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierEvent\Model\Rule\Validator;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\ValidatorException;
use MSP\NotifierEvent\Model\GetAutomaticTemplateId;
use MSP\NotifierEventApi\Api\Data\RuleInterface;
use MSP\NotifierEventApi\Model\Rule\Validator\ValidateRuleInterface;
use MSP\NotifierTemplateApi\Model\TemplateGetter\TemplateGetterInterface;

class ValidateTemplateId implements ValidateRuleInterface
{
    /**
     * @var TemplateGetterInterface
     */
    private $templateGetter;

    public function __construct(
        TemplateGetterInterface $templateGetter
    ) {
        $this->templateGetter = $templateGetter;
    }

    /**
     * @inheritDoc
     */
    public function execute(RuleInterface $rule): bool
    {
        if ($rule->getTemplateId() === GetAutomaticTemplateId::AUTOMATIC_TEMPLATE_ID) {
            return true;
        }

        if (!trim($rule->getTemplateId())) {
            throw new ValidatorException(__('Template is required'));
        }

        try {
            $template = $this->templateGetter->getTemplate('', $rule->getTemplateId());
        } catch (NoSuchEntityException $e) {
            $template = null;
        }

        if (empty($template)) {
            throw new ValidatorException(__('Invalid or unknown template id %1', $rule->getTemplateId()));
        }

        return true;
    }
}
