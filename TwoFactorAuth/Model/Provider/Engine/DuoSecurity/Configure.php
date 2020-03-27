<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\DuoSecurity;

use Magento\Integration\Model\Oauth\TokenFactory;
use Magento\TwoFactorAuth\Api\Data\DuoDataInterface;
use Magento\TwoFactorAuth\Api\Data\DuoDataInterfaceFactory;
use Magento\TwoFactorAuth\Api\DuoConfigureInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\DuoSecurity;
use Magento\TwoFactorAuth\Model\UserAuthenticator;

/**
 * Configure duo
 */
class Configure implements DuoConfigureInterface
{
    /**
     * @var UserAuthenticator
     */
    private $userAuthenticator;

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
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var Authenticate
     */
    private $authenticate;

    /**
     * @param UserAuthenticator $userAuthenticator
     * @param DuoSecurity $duo
     * @param TokenFactory $tokenFactory
     * @param DuoDataInterfaceFactory $dataFactory
     * @param TfaInterface $tfa
     * @param Authenticate $authenticate
     */
    public function __construct(
        UserAuthenticator $userAuthenticator,
        DuoSecurity $duo,
        TokenFactory $tokenFactory,
        DuoDataInterfaceFactory $dataFactory,
        TfaInterface $tfa,
        Authenticate $authenticate
    ) {
        $this->userAuthenticator = $userAuthenticator;
        $this->duo = $duo;
        $this->tokenFactory = $tokenFactory;
        $this->dataFactory = $dataFactory;
        $this->tfa = $tfa;
        $this->authenticate = $authenticate;
    }

    /**
     * @inheritDoc
     */
    public function getConfigurationData(int $userId, string $tfaToken): DuoDataInterface
    {
        $user = $this->userAuthenticator->authenticateWithTokenAndProvider($userId, $tfaToken, DuoSecurity::CODE);

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
    public function activate(int $userId, string $tfaToken, string $signatureResponse): string
    {
        $user = $this->userAuthenticator->authenticateWithTokenAndProvider($userId, $tfaToken, DuoSecurity::CODE);

        $this->authenticate->assertResponseIsValid($user, $signatureResponse);
        $this->tfa->getProviderByCode(DuoSecurity::CODE)
            ->activate($userId);

        return $this->tokenFactory->create()
            ->createAdminToken($userId)
            ->getToken();
    }
}
