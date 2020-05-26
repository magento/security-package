<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Exception\LocalizedException;
use Magento\TwoFactorAuth\Api\Data\U2fWebAuthnRequestInterface;
use Magento\TwoFactorAuth\Api\Data\U2fWebAuthnRequestInterfaceFactory;
use Magento\TwoFactorAuth\Api\U2fKeyConfigureInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use Magento\TwoFactorAuth\Model\AlertInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey;
use Magento\TwoFactorAuth\Model\UserAuthenticator;

/**
 * Configures the u2f key provider
 */
class Configure implements U2fKeyConfigureInterface
{
    private const REGISTER_CHALLENGE_KEY = 'webapi_register_challenge';

    /**
     * @var U2fKey
     */
    private $u2fKey;

    /**
     * @var UserAuthenticator
     */
    private $userAuthenticator;

    /**
     * @var UserConfigManagerInterface
     */
    private $configManager;

    /**
     * @var U2fWebAuthnRequestInterfaceFactory
     */
    private $authnInterfaceFactory;

    /**
     * @var AlertInterface
     */
    private $alert;

    /**
     * @var Json
     */
    private $json;

    /**
     * @param U2fKey $u2fKey
     * @param UserAuthenticator $userAuthenticator
     * @param UserConfigManagerInterface $configManager
     * @param U2fWebAuthnRequestInterfaceFactory $authnInterfaceFactory
     * @param AlertInterface $alert
     * @param Json $json
     */
    public function __construct(
        U2fKey $u2fKey,
        UserAuthenticator $userAuthenticator,
        UserConfigManagerInterface $configManager,
        U2fWebAuthnRequestInterfaceFactory $authnInterfaceFactory,
        AlertInterface $alert,
        Json $json
    ) {
        $this->u2fKey = $u2fKey;
        $this->userAuthenticator = $userAuthenticator;
        $this->configManager = $configManager;
        $this->authnInterfaceFactory = $authnInterfaceFactory;
        $this->alert = $alert;
        $this->json = $json;
    }

    /**
     * @inheritDoc
     */
    public function getRegistrationData(string $tfaToken): U2fWebAuthnRequestInterface
    {
        $user = $this->userAuthenticator->authenticateWithTokenAndProvider($tfaToken, U2fKey::CODE);
        $userId = (int)$user->getId();

        $data = $this->u2fKey->getRegisterData($user);

        $this->configManager->addProviderConfig(
            $userId,
            U2fKey::CODE,
            [self::REGISTER_CHALLENGE_KEY => $data['publicKey']['challenge']]
        );

        return $this->authnInterfaceFactory->create(
            [
                'data' => [
                    U2fWebAuthnRequestInterface::CREDENTIAL_REQUEST_OPTIONS_JSON => $this->json->serialize($data)
                ]
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function activate(string $tfaToken, string $publicKeyCredentialJson): void
    {
        $user = $this->userAuthenticator->authenticateWithTokenAndProvider($tfaToken, U2fKey::CODE);
        $userId = (int)$user->getId();

        $config = $this->configManager->getProviderConfig($userId, U2fKey::CODE);

        if (empty($config[self::REGISTER_CHALLENGE_KEY])) {
            throw new LocalizedException(__('U2f key registration was not started.'));
        }

        try {
            $this->u2fKey->registerDevice(
                $user,
                [
                    'publicKeyCredential' => $this->json->unserialize($publicKeyCredentialJson),
                    'challenge' => $config[self::REGISTER_CHALLENGE_KEY]
                ]
            );
            $this->alert->event(
                'Magento_TwoFactorAuth',
                'U2F New device registered',
                AlertInterface::LEVEL_INFO,
                $user->getUserName()
            );
        } catch (\Exception $e) {
            $this->alert->event(
                'Magento_TwoFactorAuth',
                'U2F error while adding device',
                AlertInterface::LEVEL_ERROR,
                $user->getUserName(),
                $e->getMessage()
            );
            throw $e;
        }

        $this->configManager->addProviderConfig(
            $userId,
            U2fKey::CODE,
            [self::REGISTER_CHALLENGE_KEY => null]
        );
    }
}
