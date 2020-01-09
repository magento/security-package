<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;

/**
 * @inheritDoc
 */
class IsCheckRequired implements IsCheckRequiredInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $enableConfigFlag;

    /**
     * @var bool
     */
    private $requireRequestParam;

    /**
     * @var string
     */
    private $area;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param RequestInterface $request
     * @param Config $config
     * @param string $area
     * @param string $enableConfigFlag
     * @param bool $requireRequestParam
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        RequestInterface $request,
        Config $config,
        $area = null,
        $enableConfigFlag = null,
        $requireRequestParam = null
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
        $this->enableConfigFlag = $enableConfigFlag;
        $this->requireRequestParam = $requireRequestParam;
        $this->area = $area;
        $this->request = $request;

        if (!in_array($this->area, [Area::AREA_FRONTEND, Area::AREA_ADMINHTML], true)) {
            throw new \InvalidArgumentException('Area parameter must be one of frontend or adminhtml');
        }
    }

    /**
     * Return true if area is configured to be active
     * @return bool
     */
    private function isAreaEnabled(): bool
    {
        return
            (($this->area === Area::AREA_ADMINHTML) && $this->config->isEnabledBackend()) ||
            (($this->area === Area::AREA_FRONTEND) && $this->config->isEnabledFrontend());
    }

    /**
     * Return true if current zone is enabled
     * @return bool
     */
    private function isZoneEnabled(): bool
    {
        return !$this->enableConfigFlag || $this->scopeConfig->getValue($this->enableConfigFlag);
    }

    /**
     * Return true if request if valid
     * @return bool
     */
    private function isRequestValid(): bool
    {
        return !$this->requireRequestParam || $this->request->getParam($this->requireRequestParam);
    }

    /**
     * Return true if check is required
     * @return bool
     */
    public function execute(): bool
    {
        return
            $this->isAreaEnabled() &&
            $this->isZoneEnabled() &&
            $this->isRequestValid();
    }
}
