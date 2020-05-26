<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Integration\Api\AdminTokenServiceInterface;
use Magento\TwoFactorAuth\Api\AdminTokenServiceInterface as AdminTokenServiceInterfaceApi;
use Magento\TwoFactorAuth\Api\Exception\NotificationExceptionInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\UserConfigRequestManagerInterface;
use Magento\User\Model\UserFactory;

/**
 * Handles the 2fa version of the admin access token service
 */
class AdminAccessTokenService implements AdminTokenServiceInterfaceApi
{
    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var UserConfigRequestManagerInterface
     */
    private $configRequestManager;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var AdminTokenServiceInterface
     */
    private $adminTokenService;

    /**
     * @param TfaInterface $tfa
     * @param UserConfigRequestManagerInterface $configRequestManager
     * @param UserFactory $userFactory
     * @param AdminTokenServiceInterface $adminTokenService
     */
    public function __construct(
        TfaInterface $tfa,
        UserConfigRequestManagerInterface $configRequestManager,
        UserFactory $userFactory,
        AdminTokenServiceInterface $adminTokenService
    ) {
        $this->tfa = $tfa;
        $this->configRequestManager = $configRequestManager;
        $this->userFactory = $userFactory;
        $this->adminTokenService = $adminTokenService;
    }

    /**
     * Prevent the admin token from being created via the token service
     *
     * @param string $username
     * @param string $password
     * @return string
     * @throws AuthenticationException
     * @throws LocalizedException
     * @throws InputException
     */
    public function createAdminAccessToken($username, $password): string
    {
        // No exception means valid input. Ignore the created token.
        $this->adminTokenService->createAdminAccessToken($username, $password);
        $user = $this->userFactory->create();
        $user->loadByUsername($username);
        $userId = (int)$user->getId();

        $providerCodes = [];
        $activeProviderCodes = [];
        foreach ($this->tfa->getUserProviders($userId) as $provider) {
            $providerCodes[] = $provider->getCode();
            if ($provider->isActive($userId)) {
                $activeProviderCodes[] = $provider->getCode();
            }
        }

        if (!$this->configRequestManager->isConfigurationRequiredFor($userId)) {
            throw new LocalizedException(
                // phpcs:ignore Magento2.Functions.DiscouragedFunction
                call_user_func(
                    '__',
                    'Please use the 2fa provider-specific endpoints to obtain a token.',
                    [
                        'active_providers' => $activeProviderCodes
                    ]
                )
            );
        } elseif (empty($this->tfa->getUserProviders($userId))) {
            // It is expected that available 2fa providers are selected via db or admin ui
            throw new LocalizedException(
                __('Please ask an administrator with sufficient access to configure 2FA first')
            );
        }

        try {
            $this->configRequestManager->sendConfigRequestTo($user);
        } catch (AuthorizationException|NotificationExceptionInterface $exception) {
            throw new LocalizedException(
                __('Failed to send the message. Please contact the administrator')
            );
        }

        throw new LocalizedException(
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            call_user_func(
                '__',
                'You are required to configure personal Two-Factor Authorization in order to login. '
                . 'Please check your email.',
                [
                    'providers' => $providerCodes,
                    'active_providers' => $activeProviderCodes
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function revokeAdminAccessToken($adminId): bool
    {
        return $this->adminTokenService->revokeAdminAccessToken($adminId);
    }
}
