<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\Authy;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Authy service management
 */
class Service
{
    /**
     * Configuration XML path for API key
     */
    public const XML_PATH_API_KEY = 'twofactorauth/authy/api_key';

    /**
     * Authy API endpoint
     */
    public const AUTHY_BASE_ENDPOINT = 'https://api.authy.com/';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get API key
     *
     * @return string
     */
    public function getApiKey(): string
    {
        return (string) $this->scopeConfig->getValue(static::XML_PATH_API_KEY);
    }

    /**
     * Get authy API endpoint
     *
     * @param string $path
     * @return string
     */
    public function getProtectedApiEndpoint(string $path): string
    {
        return static::AUTHY_BASE_ENDPOINT . 'protected/json/' . $path;
    }

    /**
     * Get authy API endpoint
     *
     * @param string $path
     * @return string
     */
    public function getOneTouchApiEndpoint(string $path): string
    {
        return static::AUTHY_BASE_ENDPOINT . 'onetouch/json/' . $path;
    }

    /**
     * Get error from response
     *
     * @param array|boolean $response
     * @return string|null
     */
    public function getErrorFromResponse($response): ?string
    {
        if ($response === false) {
            return 'Invalid authy webservice response';
        }

        if (!isset($response['success']) || !$response['success']) {
            return $response['message'];
        }

        return null;
    }
}
