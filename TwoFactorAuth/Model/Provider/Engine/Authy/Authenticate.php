<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\Authy;

use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\Integration\Api\AdminTokenServiceInterface;
use Magento\TwoFactorAuth\Api\AuthyAuthenticateInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Model\AlertInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Authy;
use Magento\User\Api\Data\UserInterface;
use Magento\User\Model\UserFactory;

/**
 * Authenticate a user with authy
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
     * @var TfaInterface
     */
    private $tfa;

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
     * @param TfaInterface $tfa
     * @param OneTouch $oneTouch
     */
    public function __construct(
        UserFactory $userFactory,
        Authy $authy,
        AlertInterface $alert,
        DataObjectFactory $dataObjectFactory,
        AdminTokenServiceInterface $adminTokenService,
        Token $authyToken,
        TfaInterface $tfa,
        OneTouch $oneTouch
    ) {
        $this->userFactory = $userFactory;
        $this->authy = $authy;
        $this->alert = $alert;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->adminTokenService = $adminTokenService;
        $this->authyToken = $authyToken;
        $this->tfa = $tfa;
        $this->oneTouch = $oneTouch;
    }

    /**
     * @inheritDoc
     */
    public function authenticateWithToken(string $username, string $password, string $otp): string
    {
        $user = $this->getUser($username);

        if (!$this->tfa->getProviderIsAllowed((int)$user->getId(), Authy::CODE)) {
            throw new WebApiException(__('Provider is not allowed.'));
        } elseif (!$this->tfa->getProviderByCode(Authy::CODE)->isActive((int)$user->getId())) {
            throw new WebApiException(__('Provider is not configured.'));
        }

        $token = $this->adminTokenService->createAdminAccessToken($username, $password);

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
    public function sendToken(string $username, string $password, string $via): bool
    {
        $user = $this->getUser($username);

        if (!$this->tfa->getProviderIsAllowed((int)$user->getId(), Authy::CODE)) {
            throw new WebApiException(__('Provider is not allowed.'));
        } elseif (!$this->tfa->getProviderByCode(Authy::CODE)->isActive((int)$user->getId())) {
            throw new WebApiException(__('Provider is not configured.'));
        }

        if ($via === 'onetouch') {
            $this->oneTouch->request($user);
        } else {
            $this->authyToken->request($user, $via);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function authenticateWithOnetouch(string $username, string $password): string
    {
        $user = $this->getUser($username);

        if (!$this->tfa->getProviderIsAllowed((int)$user->getId(), Authy::CODE)) {
            throw new WebApiException(__('Provider is not allowed.'));
        } elseif (!$this->tfa->getProviderByCode(Authy::CODE)->isActive((int)$user->getId())) {
            throw new WebApiException(__('Provider is not configured.'));
        }

        try {
            $res = $this->oneTouch->verify($user);
            if ($res === 'approved') {
                return $this->adminTokenService->create()
                    ->createAdminToken((int)$user->getId())
                    ->getToken();
            } else {
                $this->alert->event(
                    'Magento_TwoFactorAuth',
                    'Authy onetouch auth denied',
                    AlertInterface::LEVEL_WARNING,
                    $user->getUserName()
                );

                throw new WebApiException(__('Onetouch prompt was denied or timed out.'));
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
