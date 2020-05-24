<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\Authy;

use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Integration\Api\AdminTokenServiceInterface;
use Magento\TwoFactorAuth\Api\AuthyAuthenticateInterface;
use Magento\TwoFactorAuth\Model\AlertInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Authy;
use Magento\TwoFactorAuth\Model\UserAuthenticator;
use Magento\User\Api\Data\UserInterface;
use Magento\User\Model\UserFactory;

/**
 * Authenticate a user with authy
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Authenticate implements AuthyAuthenticateInterface
{
    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var Authy
     */
    private $authy;

    /**
     * @var AlertInterface
     */
    private $alert;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var AdminTokenServiceInterface
     */
    private $adminTokenService;

    /**
     * @var Token
     */
    private $authyToken;

    /**
     * @var UserAuthenticator
     */
    private $userAuthenticator;

    /**
     * @var OneTouch
     */
    private $oneTouch;

    /**
     * @param UserFactory $userFactory
     * @param Authy $authy
     * @param AlertInterface $alert
     * @param DataObjectFactory $dataObjectFactory
     * @param AdminTokenServiceInterface $adminTokenService
     * @param Token $authyToken
     * @param UserAuthenticator $userAuthenticator
     * @param OneTouch $oneTouch
     */
    public function __construct(
        UserFactory $userFactory,
        Authy $authy,
        AlertInterface $alert,
        DataObjectFactory $dataObjectFactory,
        AdminTokenServiceInterface $adminTokenService,
        Token $authyToken,
        UserAuthenticator $userAuthenticator,
        OneTouch $oneTouch
    ) {
        $this->userFactory = $userFactory;
        $this->authy = $authy;
        $this->alert = $alert;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->adminTokenService = $adminTokenService;
        $this->authyToken = $authyToken;
        $this->userAuthenticator = $userAuthenticator;
        $this->oneTouch = $oneTouch;
    }

    /**
     * @inheritDoc
     */
    public function createAdminAccessTokenWithCredentials(string $username, string $password, string $otp): string
    {
        $token = $this->adminTokenService->createAdminAccessToken($username, $password);

        $user = $this->getUser($username);
        $this->userAuthenticator->assertProviderIsValidForUser((int)$user->getId(), Authy::CODE);

        try {
            $this->authy->verify($user, $this->dataObjectFactory->create([
                'data' => [
                    'tfa_code' => $otp
                ],
            ]));

            return $token;
        } catch (\Exception $e) {
            $this->alert->event(
                'Magento_TwoFactorAuth',
                'Authy error',
                AlertInterface::LEVEL_ERROR,
                $user->getUserName(),
                $e->getMessage()
            );
            throw $e;
        }
    }

    /**
     * @inheritDoc
     */
    public function sendToken(string $username, string $password, string $via): void
    {
        $this->adminTokenService->createAdminAccessToken($username, $password);

        $user = $this->getUser($username);
        $this->userAuthenticator->assertProviderIsValidForUser((int)$user->getId(), Authy::CODE);

        if ($via === 'onetouch') {
            $this->oneTouch->request($user);
        } else {
            $this->authyToken->request($user, $via);
        }
    }

    /**
     * @inheritDoc
     */
    public function creatAdminAccessTokenWithOneTouch(string $username, string $password): string
    {
        $token = $this->adminTokenService->createAdminAccessToken($username, $password);

        $user = $this->getUser($username);
        $this->userAuthenticator->assertProviderIsValidForUser((int)$user->getId(), Authy::CODE);

        try {
            $res = $this->oneTouch->verify($user);
            if ($res === 'approved') {
                return $token;
            } else {
                $this->alert->event(
                    'Magento_TwoFactorAuth',
                    'Authy onetouch auth denied',
                    AlertInterface::LEVEL_WARNING,
                    $user->getUserName()
                );

                throw new LocalizedException(__('Onetouch prompt was denied or timed out.'));
            }
        } catch (\Exception $e) {
            $this->alert->event(
                'Magento_TwoFactorAuth',
                'Authy onetouch error',
                AlertInterface::LEVEL_ERROR,
                $user->getUserName(),
                $e->getMessage()
            );

            throw $e;
        }
    }

    /**
     * Retrieve a user using the username
     *
     * @param string $username
     * @return UserInterface
     * @throws AuthenticationException
     */
    private function getUser(string $username): UserInterface
    {
        $user = $this->userFactory->create();
        $user->loadByUsername($username);
        $userId = (int)$user->getId();
        if ($userId === 0) {
            throw new AuthenticationException(__(
                'The account sign-in was incorrect or your account is disabled temporarily. '
                . 'Please wait and try again later.'
            ));
        }

        return $user;
    }
}
