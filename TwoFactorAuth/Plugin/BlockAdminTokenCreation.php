<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Plugin;

use Magento\Framework\Authorization\PolicyInterface;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Webapi\Exception as WebapiException;
use Magento\Integration\Model\AdminTokenService;
use Magento\Integration\Model\CredentialsValidator;
use Magento\Integration\Model\Oauth\Token\RequestThrottler;
use Magento\TwoFactorAuth\Api\Exception\NotificationExceptionInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\UserConfigRequestManagerInterface;
use Magento\User\Model\User;

/**
 * Prevent the default token creation
 */
class BlockAdminTokenCreation
{
    /**
     * @var CredentialsValidator
     */
    private $credentialsValidator;

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var UserConfigRequestManagerInterface
     */
    private $configRequestManager;

    /**
     * @var PolicyInterface
     */
    private $auth;

    /**
     * @var RequestThrottler
     */
    private $requestThrottler;

    /**
     * @var User
     */
    private $adminUser;

    /**
     * @param CredentialsValidator $credentialsValidator
     * @param User $adminUser
     * @param TfaInterface $tfa
     * @param UserConfigRequestManagerInterface $configRequestManager
     * @param PolicyInterface $auth
     * @param RequestThrottler $requestThrottler
     */
    public function __construct(
        CredentialsValidator $credentialsValidator,
        User $adminUser,
        TfaInterface $tfa,
        UserConfigRequestManagerInterface $configRequestManager,
        PolicyInterface $auth,
        RequestThrottler $requestThrottler
    ) {
        $this->credentialsValidator = $credentialsValidator;
        $this->tfa = $tfa;
        $this->configRequestManager = $configRequestManager;
        $this->auth = $auth;
        $this->requestThrottler = $requestThrottler;
        $this->adminUser = $adminUser;
    }

    /**
     * Intercept valid login attempt for use with tfa
     *
     * @param AdminTokenService $subject
     * @param string $username
     * @param string $password
     * @return bool|null
     * @throws WebapiException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeCreateAdminAccessToken(
        AdminTokenService $subject,
        string $username,
        string $password
    ): void {
        $user = $this->getUserWithCredentials($username, $password);
        if (!$user || !$user->getId()) {
            // Default behavior
            return;
        }

        if (!$this->configRequestManager->isConfigurationRequiredFor((int)$user->getId())) {
            throw new WebapiException(
                __(
                    'Please use the 2fa provider-specific endpoints to obtain a token.'
                )
            );
        } elseif (empty($this->tfa->getUserProviders((int)$user->getId()))) {
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
            __(
                'You are required to configure personal Two-Factor Authorization in order to login. '
                . 'Please check your email.'
            )
        );
    }

    /**
     * Check if the given credentials are a valid admin account
     *
     * @param string $username
     * @param string $password
     * @return User|null
     */
    private function getUserWithCredentials(string $username, string $password): ?User
    {
        try {
            $this->credentialsValidator->validate($username, $password);
            $this->requestThrottler->throttle($username, RequestThrottler::USER_TYPE_ADMIN);

            return $this->adminUser->login($username, $password);
        } catch (\Exception $e) {
            return null;
        }
    }
}
