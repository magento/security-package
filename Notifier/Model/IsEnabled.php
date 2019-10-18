<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Notifier\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\NotifierApi\Api\IsEnabledInterface;

class IsEnabled implements IsEnabledInterface
{
    /**
     * Configuration path for enabled flag
     */
    private const XML_PATH_ENABLED = 'msp_notifier/general/enabled';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * IsEnabled constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Return true if module is enabled
     * @return bool
     */
    public function execute(): bool
    {
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_ENABLED);
    }
}
