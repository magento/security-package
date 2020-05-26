<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TwoFactorAuth\Api\ProviderInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use Magento\TwoFactorAuth\Api\EngineInterface;

/**
 * @inheritDoc
 */
class Provider implements ProviderInterface
{
    /**
     * @var EngineInterface
     */
    private $engine;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $name;

    /**
     * @var UserConfigManagerInterface
     */
    private $userConfigManager;

    /**
     * @var string
     */
    private $configureAction;

    /**
     * @var string
     */
    private $authAction;

    /**
     * @var string[]
     */
    private $extraActions;

    /**
     * @var bool
     */
    private $canReset;

    /**
     * @var string
     */
    private $icon;

    /**
     * @param EngineInterface $engine
     * @param UserConfigManagerInterface $userConfigManager
     * @param string $code
     * @param string $name
     * @param string $icon
     * @param string $configureAction
     * @param string $authAction
     * @param array $extraActions
     * @param bool $canReset
     */
    public function __construct(
        EngineInterface $engine,
        UserConfigManagerInterface $userConfigManager,
        string $code,
        string $name,
        string $icon,
        string $configureAction,
        string $authAction,
        array $extraActions = [],
        bool $canReset = true
    ) {
        $this->engine = $engine;
        $this->userConfigManager = $userConfigManager;
        $this->code = $code;
        $this->name = $name;
        $this->configureAction = $configureAction;
        $this->authAction = $authAction;
        $this->extraActions = $extraActions;
        $this->canReset = $canReset;
        $this->icon = $icon;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return $this->getEngine()->isEnabled();
    }

    /**
     * @inheritDoc
     */
    public function getEngine(): EngineInterface
    {
        return $this->engine;
    }

    /**
     * @inheritDoc
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @inheritDoc
     */
    public function isResetAllowed(): bool
    {
        return $this->canReset;
    }

    /**
     * @inheritdoc
     */
    public function resetConfiguration(int $userId): void
    {
        $this->userConfigManager->setProviderConfig($userId, $this->getCode(), null);
    }

    /**
     * @inheritdoc
     */
    public function isConfigured(int $userId): bool
    {
        return $this->getConfiguration($userId) !== null;
    }

    /**
     * Retrieve user's configuration
     *
     * @param int $userId
     * @return array|null
     * @throws NoSuchEntityException
     */
    private function getConfiguration(int $userId): ?array
    {
        return $this->userConfigManager->getProviderConfig($userId, $this->getCode());
    }

    /**
     * @inheritdoc
     */
    public function isActive(int $userId): bool
    {
        return $this->userConfigManager->isProviderConfigurationActive($userId, $this->getCode());
    }

    /**
     * @inheritdoc
     */
    public function activate(int $userId): void
    {
        $this->userConfigManager->activateProviderConfiguration($userId, $this->getCode());
    }

    /**
     * @inheritdoc
     */
    public function getConfigureAction(): string
    {
        return $this->configureAction;
    }

    /**
     * @inheritdoc
     */
    public function getAuthAction(): string
    {
        return $this->authAction;
    }

    /**
     * @inheritdoc
     */
    public function getExtraActions(): array
    {
        return $this->extraActions;
    }
}
