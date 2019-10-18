<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEvent\Model\Rule\Validator;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\ValidatorException;
use Magento\NotifierEvent\Model\GetAutomaticTemplateId;
use Magento\NotifierEventApi\Api\Data\RuleInterface;
use Magento\NotifierEventApi\Model\Rule\Validator\ValidateRuleInterface;
use Magento\NotifierTemplateApi\Model\TemplateGetter\TemplateGetterInterface;

class ValidateTemplateId implements ValidateRuleInterface
{
    /**
     * @var TemplateGetterInterface
     */
    private $templateGetter;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * ValidateTemplateId constructor.
     * @param TemplateGetterInterface $templateGetter
     * @param SerializerInterface $serializer
     */
    public function __construct(
        TemplateGetterInterface $templateGetter,
        SerializerInterface $serializer
    ) {
        $this->templateGetter = $templateGetter;
        $this->serializer = $serializer;
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

        $channels = $this->serializer->unserialize($rule->getChannelsCodes());

        foreach ($channels as $channel) {
            try {
                $this->templateGetter->getTemplate($channel, $rule->getTemplateId());
            } catch (NoSuchEntityException $e) {
                throw new ValidatorException(
                    __('Invalid or unknown template id %1 for channel %2', $rule->getTemplateId(), $channel)
                );
            }
        }

        return true;
    }
}
