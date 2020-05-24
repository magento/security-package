<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\User\Api\Data\UserInterface;
use Magento\TwoFactorAuth\Api\EngineInterface;

/**
 * Duo Security engine
 */
class DuoSecurity implements EngineInterface
{
    /**
     * Engine code
     */
    public const CODE = 'duo_security'; // Must be the same as defined in di.xml

    /**
     * Duo request prefix
     */
    public const DUO_PREFIX = 'TX';

    /**
     * Duo app prefix
     */
    public const APP_PREFIX = 'APP';

    /**
     * Duo auth prefix
     */
    public const AUTH_PREFIX = 'AUTH';

    /**
     * Duo expire time
     */
    public const DUO_EXPIRE = 300;

    /**
     * Application expire time
     */
    public const APP_EXPIRE = 3600;

    /**
     * Configuration XML path for enabled flag
     */
    public const XML_PATH_ENABLED = 'twofactorauth/duo/enabled';

    /**
     * Configuration XML path for integration key
     */
    public const XML_PATH_INTEGRATION_KEY = 'twofactorauth/duo/integration_key';

    /**
     * Configuration XML path for secret key
     */
    public const XML_PATH_SECRET_KEY = 'twofactorauth/duo/secret_key';

    /**
     * Configuration XML path for host name
     */
    public const XML_PATH_API_HOSTNAME = 'twofactorauth/duo/api_hostname';

    /**
     * Configuration XML path for application key
     */
    public const XML_PATH_APPLICATION_KEY = 'twofactorauth/duo/application_key';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get API hostname
     *
     * @return string
     */
    public function getApiHostname(): string
    {
        return $this->scopeConfig->getValue(static::XML_PATH_API_HOSTNAME);
    }

    /**
     * Get application key
     *
     * @return string
     */
    private function getApplicationKey(): string
    {
        return $this->scopeConfig->getValue(static::XML_PATH_APPLICATION_KEY);
    }

    /**
     * Get secret key
     *
     * @return string
     */
    private function getSecretKey(): string
    {
        return $this->scopeConfig->getValue(static::XML_PATH_SECRET_KEY);
    }

    /**
     * Get integration key
     *
     * @return string
     */
    private function getIntegrationKey(): string
    {
        return $this->scopeConfig->getValue(static::XML_PATH_INTEGRATION_KEY);
    }

    /**
     * Sign values
     *
     * @param string $key
     * @param string $values
     * @param string $prefix
     * @param int $expire
     * @param int $time
     * @return string
     */
    private function signValues(string $key, string $values, string $prefix, int $expire, int $time): string
    {
        $exp = $time + $expire;
        $cookie = $prefix . '|' . base64_encode($values . '|' . $exp);

        $sig = hash_hmac('sha1', $cookie, $key);
        return $cookie . '|' . $sig;
    }

    /**
     * Parse signed values and return username
     *
     * @param string $key
     * @param string $val
     * @param string $prefix
     * @param int $time
     * @return string|null
     */
    private function parseValues(string $key, string $val, string $prefix, int $time): ?string
    {
        $integrationKey = $this->getIntegrationKey();

        $timestamp = ($time ? $time : time());

        $parts = explode('|', $val);
        if (count($parts) !== 3) {
            return null;
        }
        [$uPrefix, $uB64, $uSig] = $parts;

        $sig = hash_hmac('sha1', $uPrefix . '|' . $uB64, $key);
        if (hash_hmac('sha1', $sig, $key) !== hash_hmac('sha1', $uSig, $key)) {
            return null;
        }

        if ($uPrefix !== $prefix) {
            return null;
        }

        // @codingStandardsIgnoreStart
        $cookieParts = explode('|', base64_decode($uB64));
        // @codingStandardsIgnoreEnd

        if (count($cookieParts) !== 3) {
            return null;
        }
        [$user, $uIkey, $exp] = $cookieParts;

        if ($uIkey !== $integrationKey) {
            return null;
        }
        if ($timestamp >= (int) $exp) {
            return null;
        }

        return $user;
    }

    /**
     * Get request signature
     *
     * @param UserInterface $user
     * @return string
     */
    public function getRequestSignature(UserInterface $user): string
    {
        $time = time();

        $values = $user->getUserName() . '|' . $this->getIntegrationKey();
        $duoSignature = $this->signValues(
            $this->getSecretKey(),
            $values,
            static::DUO_PREFIX,
            static::DUO_EXPIRE,
            $time
        );
        $appSignature = $this->signValues(
            $this->getApplicationKey(),
            $values,
            static::APP_PREFIX,
            static::APP_EXPIRE,
            $time
        );

        return $duoSignature . ':' . $appSignature;
    }

    /**
     * @inheritDoc
     */
    public function verify(UserInterface $user, DataObject $request): bool
    {
        $time = time();

        $signatures = explode(':', (string)$request->getData('sig_response'));
        if (count($signatures) !== 2) {
            return false;
        }
        [$authSig, $appSig] = $signatures;

        $authUser = $this->parseValues($this->getSecretKey(), $authSig, static::AUTH_PREFIX, $time);
        $appUser = $this->parseValues($this->getApplicationKey(), $appSig, static::APP_PREFIX, $time);

        return (($authUser === $appUser) && ($appUser === $user->getUserName()));
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        try {
            return !!$this->getApiHostname() &&
                !!$this->getIntegrationKey() &&
                !!$this->getSecretKey();
        } catch (\TypeError $exception) {
            //At least one of the methods returned null instead of a string
            return false;
        }
    }
}
