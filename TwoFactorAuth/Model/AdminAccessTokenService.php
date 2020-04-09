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
use Magento\Framework\Webapi\Exception as WebapiException;
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
     * Prevent the admin token from being created with this api
     *
     * @param string $username
     * @param string $password
     * @return void
     * @throws AuthenticationException
     * @throws WebapiException
     * @throws InputException
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function createAdminAccessToken(string $username, string $password): void
    {
        // No exception means valid input. Ignore the created token.
        $this->adminTokenService->createAdminAccessToken($username, $password);
        $user = $this->userFactory->create();
        $user->loadByUsername($username);
        $userId = (int)$user->getId();
        if ($userId === 0) {
            throw new AuthenticationException(__(
                'The account sign-in was incorrect or your account is disabled temporarily. '
                . 'Please wait and try again later.'
            ));
        }

        $providerCodes = [];
        $activeProviderCodes = [];
        foreach ($this->tfa->getUserProviders($userId) as $provider) {
            $providerCodes[] = $provider->getCode();
            if ($provider->isActive($userId)) {
                $activeProviderCodes[] = $provider->getCode();
            }
        }

        if (!$this->configRequestManager->isConfigurationRequiredFor($userId)) {
            throw new WebapiException(
                __('Please use the 2fa provider-specific endpoints to obtain a token.'),
                0,
                WebapiException::HTTP_UNAUTHORIZED,
                [
                    'active_providers' => $activeProviderCodes
                ]
            );
        } elseif (empty($this->tfa->getUserProviders($userId))) {
            // It is expected that available 2fa providers are selected via db or admin ui
            throw new WebapiException(
                __('Please ask an administrator with sufficient access to configure 2FA first')
            );
        }

        try {
            $this->configRequestManager->sendConfigRequestTo($user);
        } catch (AuthorizationException|NotificationExceptionInterface $exception) {
            throw new WebapiException(
                __('Failed to send the message. Please contact the administrator')
            );
        }

        throw new WebapiException(
            __('You are required to configure personal Two-Factor Authorization in order to login. '
            . 'Please check your email.'),
            0,
            WebapiException::HTTP_UNAUTHORIZED,
            [
                'providers' => $providerCodes,
                'active_providers' => $activeProviderCodes
            ]
        );
    }
}
