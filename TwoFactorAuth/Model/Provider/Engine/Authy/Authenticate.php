<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\Authy;

use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\TwoFactorAuth\Api\AuthyAuthenticateInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Model\AlertInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Authy;
use Magento\TwoFactorAuth\Model\UserAuthenticator;
use Magento\Integration\Model\Oauth\TokenFactory as TokenModelFactory;

/**
 * Authenticate a user with authy
 */
class Authenticate implements AuthyAuthenticateInterface
{
    /**
     * @var UserAuthenticator
     */
    private $userAuthenticator;

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
     * @var TokenModelFactory
     */
    private $tokenFactory;

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
     * @param UserAuthenticator $userAuthenticator
     * @param Authy $authy
     * @param AlertInterface $alert
     * @param DataObjectFactory $dataObjectFactory
     * @param TokenModelFactory $tokenFactory
     * @param Token $authyToken
     * @param TfaInterface $tfa
     * @param OneTouch $oneTouch
     */
    public function __construct(
        UserAuthenticator $userAuthenticator,
        Authy $authy,
        AlertInterface $alert,
        DataObjectFactory $dataObjectFactory,
        TokenModelFactory $tokenFactory,
        Token $authyToken,
        TfaInterface $tfa,
        OneTouch $oneTouch
    ) {
        $this->userAuthenticator = $userAuthenticator;
        $this->authy = $authy;
        $this->alert = $alert;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->tokenFactory = $tokenFactory;
        $this->authyToken = $authyToken;
        $this->tfa = $tfa;
        $this->oneTouch = $oneTouch;
    }

    /**
     * @inheritDoc
     */
    public function authenticateWithToken(string $username, string $password, string $otp): string
    {
        $user = $this->userAuthenticator->authenticateWithCredentials($username, $password);

        if (!$this->tfa->getProviderIsAllowed((int)$user->getId(), Authy::CODE)) {
            throw new WebApiException(__('Provider is not allowed.'));
        } elseif (!$this->tfa->getProviderByCode(Authy::CODE)->isActive((int)$user->getId())) {
            throw new WebApiException(__('Provider is not configured.'));
        }

        try {
            $this->authy->verify($user, $this->dataObjectFactory->create([
                'data' => [
                    'tfa_code' => $otp
                ],
            ]));

            return $this->tokenFactory->create()
                ->createAdminToken((int)$user->getId())
                ->getToken();
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
        $user = $this->userAuthenticator->authenticateWithCredentials($username, $password);

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
        $user = $this->userAuthenticator->authenticateWithCredentials($username, $password);

        if (!$this->tfa->getProviderIsAllowed((int)$user->getId(), Authy::CODE)) {
            throw new WebApiException(__('Provider is not allowed.'));
        } elseif (!$this->tfa->getProviderByCode(Authy::CODE)->isActive((int)$user->getId())) {
            throw new WebApiException(__('Provider is not configured.'));
        }

        try {
            $res = $this->oneTouch->verify($user);
            if ($res === 'approved') {
                return $this->tokenFactory->create()
                    ->createAdminToken((int)$user->getId())
                    ->getToken();
            } else {
                $this->alert->event(
                    'Magento_TwoFactorAuth',
                    'Authy onetouch auth denied',
                    AlertInterface::LEVEL_WARNING,
                    $user->getUserName()
                );

                throw new AuthorizationException(__('Onetouch prompt was denied or timed out.'));
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
}
