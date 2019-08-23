<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEvent\Model;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\NotifierEventApi\Api\Data\RuleInterface;
use Magento\NotifierEventApi\Model\GetAutomaticTemplateIdInterface;
use Magento\NotifierTemplateApi\Model\TemplateGetter\TemplateGetterInterface;

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
