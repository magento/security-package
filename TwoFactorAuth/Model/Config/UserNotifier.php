<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;

/**
 * Represents configuration for notifying the user
 */
class UserNotifier
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param UrlInterface $url
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        UrlInterface $url,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->url = $url;
    }

    /**
     * Get the url to send to the user for configuring personal 2fa settings
     *
     * @param string $tfaToken
     * @return string
     */
    public function getPersonalRequestConfigUrl(string $tfaToken): string
    {
        return $this->getRequestConfigUrl($tfaToken);
    }

    /**
     * Get the url to send to the user for configuring global 2fa settings
     *
     * @param string $tfaToken
     * @return string
     */
    public function getAppRequestConfigUrl(string $tfaToken): string
    {
        return $this->getRequestConfigUrl($tfaToken);
    }

    /**
     * Get the default config url
     *
     * @param string $tfaToken
     * @return string
     */
    private function getRequestConfigUrl(string $tfaToken)
    {
        return $this->url->getUrl('tfa/tfa/index', ['tfat' => $tfaToken]);
    }
}
