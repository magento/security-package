<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey\WebAuthn;
use Magento\User\Api\Data\UserInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use Magento\TwoFactorAuth\Api\EngineInterface;

/**
 * UbiKey engine
 */
class U2fKey implements EngineInterface
{
    /**
     * The config path for the domain to use when issuing challenged from the web api
     */
    const XML_PATH_WEBAPI_DOMAIN = 'twofactorauth/u2fkey/webapi_challenge_domain';

    /**
     * Engine code
     *
     * Must be the same as defined in di.xml
     */
    public const CODE = 'u2fkey';

    /**
     * @var UserConfigManagerInterface
     */
    private $userConfigManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var WebAuthn
     */
    private $webAuthn;

    /**
     * @param StoreManagerInterface $storeManager
     * @param UserConfigManagerInterface $userConfigManager
     * @param WebAuthn $webAuthn
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        UserConfigManagerInterface $userConfigManager,
        WebAuthn $webAuthn
    ) {
        $this->userConfigManager = $userConfigManager;
        $this->storeManager = $storeManager;
        $this->webAuthn = $webAuthn;
    }

    /**
     * @inheritDoc
     */
    public function verify(UserInterface $user, DataObject $request): bool
    {
        $registration = $this->getRegistration($user);
        if ($registration === null) {
            throw new LocalizedException(__('Missing registration data'));
        }

        $this->webAuthn->assertCredentialDataIsValid(
            $request->getData('publicKeyCredential'),
            $registration['public_keys'],
            $request->getData('originalChallenge')
        );

        return true;
    }

    /**
     * Create the registration challenge
     *
     * @param UserInterface $user
     * @return array
     * @throws LocalizedException
     */
    public function getRegisterData(UserInterface $user): array
    {
        return $this->webAuthn->getRegisterData($user);
    }

    /**
     * Get authenticate data
     *
     * @param UserInterface $user
     * @return array
     * @throws LocalizedException
     */
    public function getAuthenticateData(UserInterface $user): array
    {
        return $this->webAuthn->getAuthenticateData($this->getRegistration($user)['public_keys']);
    }

    /**
     * Get registration information
     *
     * @param UserInterface $user
     * @return array
     * @throws NoSuchEntityException
     */
    private function getRegistration(UserInterface $user): array
    {
        $providerConfig = $this->userConfigManager->getProviderConfig((int) $user->getId(), static::CODE);

        if (!isset($providerConfig['registration'])) {
            return null;
        }

        return $providerConfig['registration'];
    }

    /**
     * Register a new key
     *
     * @param UserInterface $user
     * @param array $data
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Validation\ValidationException
     */
    public function registerDevice(UserInterface $user, array $data): void
    {
        $publicKey = $this->webAuthn->getPublicKeyFromRegistrationData($data);

        $this->userConfigManager->addProviderConfig((int) $user->getId(), static::CODE, [
            'registration' => [
                'public_keys' => [$publicKey]
            ]
        ]);
        $this->userConfigManager->activateProviderConfiguration((int) $user->getId(), static::CODE);
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return true;
    }
}
