<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierEvent\Model;

use Exception;
use MSP\NotifierEventApi\Api\Data\RuleInterface;
use MSP\NotifierEventApi\Model\GetAutomaticTemplateIdInterface;
use MSP\NotifierTemplateApi\Model\TemplateGetter\TemplateGetterInterface;

class GetAutomaticTemplateId implements GetAutomaticTemplateIdInterface
{
    /**
     * Default template id
     */
    private const DEFAULT_TEMPLATE = 'event:_default';

    /**
     * Default event prefix
     */
    private const EVENT_PREFIX = 'event:';

    /**
     * @var TemplateGetterInterface
     */
    private $templateGetterPool;

    /**
     * @param TemplateGetterInterface $templateGetterPool
     */
    public function __construct(
        TemplateGetterInterface $templateGetterPool
    ) {
        $this->templateGetterPool = $templateGetterPool;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(RuleInterface $rule, string $eventName, array $data = []): string
    {
        try {
            $this->templateGetterPool->getTemplate('', self::EVENT_PREFIX . $eventName);
            return self::EVENT_PREFIX . $eventName;
        } catch (Exception $e) {
            return self::DEFAULT_TEMPLATE;
        }
    }
}
