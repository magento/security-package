<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TwoFactorAuth\Api\ProviderInterface;
use Magento\TwoFactorAuth\Api\ProviderPoolInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;

/**
 * @inheritDoc
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Tfa implements TfaInterface
{
    /**
     * @var null|string[]
     */
    private $allowedUrls = null;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var UserConfigManagerInterface
     */
    private $userConfigManager;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ProviderPoolInterface
     */
    private $providerPool;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param UserConfigManagerInterface $userConfigManager
     * @param ProviderPoolInterface $providerPool
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        UserConfigManagerInterface $userConfigManager,
        ProviderPoolInterface $providerPool
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->userConfigManager = $userConfigManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->providerPool = $providerPool;
    }

    /**
     * @inheritdoc
     */
    public function getAllProviders(): array
    {
        return array_values($this->providerPool->getProviders());
    }

    /**
     * @inheritdoc
     */
    public function getProviderByCode(string $code): ?ProviderInterface
    {
        if ($code) {
            try {
                return $this->providerPool->getProviderByCode($code);
            } catch (NoSuchEntityException $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getAllEnabledProviders(): array
    {
        $enabledProviders = [];
        $providers = $this->getAllProviders();
        foreach ($providers as $provider) {
            if ($provider->isEnabled()) {
                $enabledProviders[] = $provider;
            }
        }

        return $enabledProviders;
    }

    /**
     * @inheritdoc
     */
    public function getProvider(string $providerCode, bool $onlyEnabled = true): ?ProviderInterface
    {
        $provider = $this->getProviderByCode($providerCode);

        if (!$provider) {
            return null;
        }

        if ($onlyEnabled && !$provider->isEnabled()) {
            return null;
        }

        return $provider;
    }

    /**
     * @inheritdoc
     */
    public function getForcedProviders(): array
    {
        $forcedProviders = [];

        $configValue = $this->scopeConfig->getValue(TfaInterface::XML_PATH_FORCED_PROVIDERS);
        if (!is_array($configValue) && $configValue) {
            $forcedProvidersCodes = preg_split('/\s*,\s*/', $configValue);
        } else {
            $forcedProvidersCodes = $configValue;
        }

        if ($forcedProvidersCodes) {
            foreach ($forcedProvidersCodes as $forcedProviderCode) {
                $provider = $this->getProvider($forcedProviderCode);
                if ($provider) {
                    $forcedProviders[] = $provider;
                }
            }
        }

        return $forcedProviders;
    }

    /**
     * @inheritdoc
     */
    public function getUserProviders(int $userId): array
    {
        return $this->getForcedProviders();
    }

    /**
     * @inheritdoc
     */
    public function getAllowedUrls(): array
    {
        if ($this->allowedUrls === null) {
            $this->allowedUrls = [
                'adminhtml_auth_login',
                'adminhtml_auth_logout',
                'adminhtml_auth_forgotpassword',
                'tfa_tfa_accessdenied',
                'tfa_tfa_requestconfig',
                'tfa_tfa_configurelater',
                'tfa_tfa_configure',
                'tfa_tfa_configurepost',
                'tfa_tfa_index'
            ];

            $providers = $this->getAllProviders();
            foreach ($providers as $provider) {
                $this->allowedUrls[] = str_replace('/', '_', $provider->getConfigureAction());
                $this->allowedUrls[] = str_replace('/', '_', $provider->getAuthAction());

                foreach (array_values($provider->getExtraActions()) as $extraAction) {
                    $this->allowedUrls[] = str_replace('/', '_', $extraAction);
                }
            }
        }

        return $this->allowedUrls;
    }

    /**
     * @inheritdoc
     */
    public function getProvidersToActivate(int $userId): array
    {
        $providers = $this->getUserProviders($userId);

        $res = [];
        foreach ($providers as $provider) {
            if (!$provider->isActive($userId)) {
                $res[] = $provider;
            }
        }

        return $res;
    }

    /**
     * @inheritdoc
     */
    public function getProviderIsAllowed(int $userId, string $providerCode): bool
    {
        $providers = $this->getUserProviders($userId);
        foreach ($providers as $provider) {
            if ($provider->getCode() === $providerCode) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function isEnabled(): bool
    {
        return true;
    }

    /**
     * Return true if a provider code is allowed
     *
     * @param int $userId
     * @param string $providerCode
     * @throws NoSuchEntityException
     */
    private function assertAllowedProvider(int $userId, string $providerCode): void
    {
        if (!$this->getProviderIsAllowed($userId, $providerCode)) {
            throw new NoSuchEntityException(__('Unknown or not enabled provider %1 for this user', $providerCode));
        }
    }

    /**
     * @inheritdoc
     */
    public function getDefaultProviderCode(int $userId): string
    {
        return $this->userConfigManager->getDefaultProvider($userId);
    }

    /**
     * Set default provider code
     *
     * @param int $userId
     * @param string $providerCode
     * @return boolean
     * @throws NoSuchEntityException
     */
    public function setDefaultProviderCode(int $userId, string $providerCode): bool
    {
        $this->assertAllowedProvider($userId, $providerCode);
        return $this->userConfigManager->setDefaultProvider($userId, $providerCode);
    }

    /**
     * @inheritdoc
     */
    public function resetProviderConfig(int $userId, string $providerCode): bool
    {
        $this->assertAllowedProvider($userId, $providerCode);
        return $this->userConfigManager->resetProviderConfig($userId, $providerCode);
    }

    /**
     * @inheritdoc
     */
    public function setProvidersCodes(int $userId, string $providersCodes): bool
    {
        if (is_string($providersCodes)) {
            $providersCodes = preg_split('/\s*,\s*/', $providersCodes);
        }

        foreach ($providersCodes as $providerCode) {
            $this->assertAllowedProvider($userId, $providerCode);
        }

        return $this->userConfigManager->setProvidersCodes($userId, $providersCodes);
    }
}
