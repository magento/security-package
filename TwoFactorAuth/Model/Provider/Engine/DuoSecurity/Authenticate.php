<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\DuoSecurity;

use Magento\Framework\DataObjectFactory;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\Integration\Model\Oauth\TokenFactory;
use Magento\TwoFactorAuth\Api\Data\DuoDataInterface;
use Magento\TwoFactorAuth\Api\Data\DuoDataInterfaceFactory;
use Magento\TwoFactorAuth\Api\DuoAuthenticateInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Model\AlertInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\DuoSecurity;
use Magento\TwoFactorAuth\Model\UserAuthenticator;
use Magento\User\Api\Data\UserInterface;

/**
 * Authenticate with duo
 */
class Authenticate implements DuoAuthenticateInterface
{
    /**
     * @var UserAuthenticator
     */
    private $userAuthenticator;

    /**
     * @var AlertInterface
     */
    private $alert;

    /**
     * @var DuoSecurity
     */
    private $duo;

    /**
     * @var TokenFactory
     */
    private $tokenFactory;

    /**
     * @var DuoDataInterfaceFactory
     */
    private $dataFactory;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @param UserAuthenticator $userAuthenticator
     * @param AlertInterface $alert
     * @param DuoSecurity $duo
     * @param TokenFactory $tokenFactory
     * @param DuoDataInterfaceFactory $dataFactory
     * @param DataObjectFactory $dataObjectFactory
     * @param TfaInterface $tfa
     */
    public function __construct(
        UserAuthenticator $userAuthenticator,
        AlertInterface $alert,
        DuoSecurity $duo,
        TokenFactory $tokenFactory,
        DuoDataInterfaceFactory $dataFactory,
        DataObjectFactory $dataObjectFactory,
        TfaInterface $tfa
    ) {
        $this->userAuthenticator = $userAuthenticator;
        $this->alert = $alert;
        $this->duo = $duo;
        $this->tokenFactory = $tokenFactory;
        $this->dataFactory = $dataFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->tfa = $tfa;
    }

    /**
     * @inheritDoc
     */
    public function getAuthenticateData(string $username, string $password): DuoDataInterface
    {
        $user = $this->userAuthenticator->authenticateWithCredentials($username, $password);
        $userId = (int)$user->getId();

        if (!$this->tfa->getProviderIsAllowed($userId, DuoSecurity::CODE)) {
            throw new WebApiException(__('Provider is not allowed.'));
        } elseif (!$this->tfa->getProviderByCode(DuoSecurity::CODE)->isActive($userId)) {
            throw new WebApiException(__('Provider is not configured.'));
        }

        return $this->dataFactory->create(
            [
                'data' => [
                    DuoDataInterface::API_HOSTNAME => $this->duo->getApiHostname(),
                    DuoDataInterface::SIGNATURE => $this->duo->getRequestSignature($user)
                ]
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function verify(string $username, string $password, string $signatureResponse): string
    {
        $user = $this->userAuthenticator->authenticateWithCredentials($username, $password);
        $userId = (int)$user->getId();

        if (!$this->tfa->getProviderIsAllowed($userId, DuoSecurity::CODE)) {
            throw new WebApiException(__('Provider is not allowed.'));
        } elseif (!$this->tfa->getProviderByCode(DuoSecurity::CODE)->isActive($userId)) {
            throw new WebApiException(__('Provider is not configured.'));
        }

        $this->assertResponseIsValid($user, $signatureResponse);

        return $this->tokenFactory->create()
            ->createAdminToken($userId)
            ->getToken();
    }

    /**
     * Assert that the given signature is valid for the user
     *
     * @param UserInterface $user
     * @param string $signatureResponse
     * @throws WebApiException
     */
    public function assertResponseIsValid(UserInterface $user, string $signatureResponse): void
    {
        $data = $this->dataObjectFactory->create(
            [
                'data' => [
                    'sig_response' => $signatureResponse
                ]
            ]
        );
        if (!$this->duo->verify($user, $data)) {
            $this->alert->event(
                'Magento_TwoFactorAuth',
                'DuoSecurity invalid auth',
                AlertInterface::LEVEL_WARNING,
                $user->getUserName()
            );

            throw new WebApiException(__('Invalid response'));
        }
    }
}
