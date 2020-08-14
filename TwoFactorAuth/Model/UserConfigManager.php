<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model;

use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use Magento\TwoFactorAuth\Model\ResourceModel\UserConfig as UserConfigResource;

/**
 * @inheritDoc
 */
class UserConfigManager implements UserConfigManagerInterface
{
    /**
     * @var array
     */
    private $configurationRegistry = [];

    /**
     * @var UserConfigFactory
     */
    private $userConfigFactory;

    /**
     * @var UserConfigResource
     */
    private $userConfigResource;

    /**
     * @param UserConfigFactory $userConfigFactory
     * @param UserConfigResource|null $userConfigResource
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        UserConfigFactory $userConfigFactory,
        UserConfigResource $userConfigResource
    ) {
        $this->userConfigFactory = $userConfigFactory;
        $this->userConfigResource = $userConfigResource;
    }

    /**
     * @inheritDoc
     */
    public function getProviderConfig(int $userId, string $providerCode): ?array
    {
        $userConfig = $this->getUserConfiguration($userId);
        $providersConfig = $userConfig->getData('config');

        if (!isset($providersConfig[$providerCode])) {
            return null;
        }

        return $providersConfig[$providerCode];
    }

    /**
     * @inheritdoc
     */
    public function setProviderConfig(int $userId, string $providerCode, ?array $config = null): bool
    {
        $userConfig = $this->getUserConfiguration($userId);
        $providersConfig = $userConfig->getData('config');

        if ($config === null) {
            if (isset($providersConfig[$providerCode])) {
                unset($providersConfig[$providerCode]);
            }
        } else {
            $providersConfig[$providerCode] = $config;
        }

        $userConfig->setData('config', $providersConfig);
        $this->userConfigResource->save($userConfig);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function addProviderConfig(int $userId, string $providerCode, ?array $config = null): bool
    {
        $userConfig = $this->getProviderConfig($userId, $providerCode);
        if ($userConfig === null) {
            $newConfig = $config;
        } else {
            $newConfig = array_merge($userConfig, $config);
        }

        return $this->setProviderConfig($userId, $providerCode, $newConfig);
    }

    /**
     * @inheritdoc
     */
    public function resetProviderConfig(int $userId, string $providerCode): bool
    {
        $this->setProviderConfig($userId, $providerCode, null);
        return true;
    }

    /**
     * Get user TFA config
     *
     * @param int $userId
     * @return UserConfig
     */
    private function getUserConfiguration(int $userId): UserConfig
    {
        if (!isset($this->configurationRegistry[$userId])) {
            /** @var $userConfig UserConfig */
            $userConfig = $this->userConfigFactory->create();
            $this->userConfigResource->load($userConfig, $userId, 'user_id');
            $userConfig->setData('user_id', $userId);

            $this->configurationRegistry[$userId] = $userConfig;
        }

        return $this->configurationRegistry[$userId];
    }

    /**
     * @inheritdoc
     */
    public function setProvidersCodes(int $userId, $providersCodes): bool
    {
        if (is_string($providersCodes)) {
            $providersCodes = preg_split('/\s*,\s*/', $providersCodes);
        }

        $userConfig = $this->getUserConfiguration($userId);
        $userConfig->setData('providers', $providersCodes);
        $this->userConfigResource->save($userConfig);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getProvidersCodes(int $userId): array
    {
        $userConfig = $this->getUserConfiguration($userId);
        return $userConfig->getData('providers');
    }

    /**
     * @inheritdoc
     */
    public function activateProviderConfiguration(int $userId, string $providerCode): bool
    {
        return $this->addProviderConfig($userId, $providerCode, [
            UserConfigManagerInterface::ACTIVE_CONFIG_KEY => true
        ]);
    }

    /**
     * @inheritdoc
     */
    public function isProviderConfigurationActive(int $userId, string $providerCode): bool
    {
        $config = $this->getProviderConfig($userId, $providerCode);
        return $config &&
            isset($config[UserConfigManagerInterface::ACTIVE_CONFIG_KEY]) &&
            $config[UserConfigManagerInterface::ACTIVE_CONFIG_KEY];
    }

    /**
     * @inheritdoc
     */
    public function setDefaultProvider(int $userId, string $providerCode): bool
    {
        $userConfig = $this->getUserConfiguration($userId);
        $userConfig->setData('default_provider', $providerCode);
        $this->userConfigResource->save($userConfig);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultProvider(int $userId): string
    {
        $userConfig = $this->getUserConfiguration($userId);
        return $userConfig->getData('default_provider') ?: '';
    }
}
