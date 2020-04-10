<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\Google;

use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Integration\Api\AdminTokenServiceInterface;
use Magento\TwoFactorAuth\Api\GoogleAuthenticateInterface;
use Magento\TwoFactorAuth\Model\AlertInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Google;
use Magento\TwoFactorAuth\Model\UserAuthenticator;
use Magento\User\Model\UserFactory;

/**
 * Authenticate with google provider
 */
class Authenticate implements GoogleAuthenticateInterface
{
    /**
     * @var Google
     */
    private $google;

    /**
     * @var UserAuthenticator
     */
    private $userAuthenticator;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var AlertInterface
     */
    private $alert;

    /**
     * @var AdminTokenServiceInterface
     */
    private $adminTokenService;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @param Google $google
     * @param UserAuthenticator $userAuthenticator
     * @param DataObjectFactory $dataObjectFactory
     * @param AlertInterface $alert
     * @param AdminTokenServiceInterface $adminTokenService
     * @param UserFactory $userFactory
     */
    public function __construct(
        Google $google,
        UserAuthenticator $userAuthenticator,
        DataObjectFactory $dataObjectFactory,
        AlertInterface $alert,
        AdminTokenServiceInterface $adminTokenService,
        UserFactory $userFactory
    ) {
        $this->google = $google;
        $this->userAuthenticator = $userAuthenticator;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->alert = $alert;
        $this->adminTokenService = $adminTokenService;
        $this->userFactory = $userFactory;
    }

    /**
     * @inheritDoc
     */
    public function createAdminAccessToken(string $username, string $password, string $otp): string
    {
        $token = $this->adminTokenService->createAdminAccessToken($username, $password);
        $user = $this->userFactory->create();
        $user->loadByUsername($username);
        $this->userAuthenticator->assertProviderIsValidForUser((int)$user->getId(), Google::CODE);

        if ($this->google->verify($user, $this->dataObjectFactory->create([
                'data' => [
                    'tfa_code' => $otp
                ],
            ]))
        ) {
            $this->alert->event(
                'Magento_TwoFactorAuth',
                'New Google Authenticator code issued',
                AlertInterface::LEVEL_INFO,
                $user->getUserName()
            );

            return $token;
        } else {
            throw new AuthorizationException(__('Invalid code.'));
        }
    }
}
