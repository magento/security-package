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
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\User\Api\Data\UserInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use Magento\TwoFactorAuth\Api\EngineInterface;
use stdClass;
use u2flib_server\Error;
use u2flib_server\Registration;
use u2flib_server\U2F;

/**
 * UbiKey engine
 */
class U2fKey implements EngineInterface
{
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
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param UserConfigManagerInterface $userConfigManager
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        UserConfigManagerInterface $userConfigManager
    ) {
        $this->userConfigManager = $userConfigManager;
        $this->storeManager = $storeManager;
    }

    /**
     * Converts array to object
     * @param array $hash
     * @return stdClass
     */
    private function hashToObject(array $hash): stdClass
    {
        // @codingStandardsIgnoreStart
        $object = new stdClass();
        // @codingStandardsIgnoreEnd
        foreach ($hash as $key => $value) {
            $object->$key = $value;
        }

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function verify(UserInterface $user, DataObject $request): bool
    {
        $u2f = $this->getU2f();

        $registration = $this->getRegistration($user);
        if ($registration === null) {
            throw new LocalizedException(__('Missing registration data'));
        }

        $requests = [$this->hashToObject($request->getData('request')[0])];
        $registrations = [$this->hashToObject($registration)];
        $response = $this->hashToObject($request->getData('response'));

        // it triggers an error in case of auth failure
        $u2f->doAuthenticate($requests, $registrations, $response);

        return true;
    }

    /**
     * Create the registration challenge
     * @return array
     * @throws LocalizedException
     * @throws Error
     */
    public function getRegisterData(): array
    {
        $u2f = $this->getU2f();
        return $u2f->getRegisterData();
    }

    /**
     * Get authenticate data
     * @param UserInterface $user
     * @return array
     * @throws LocalizedException
     * @throws Error
     */
    public function getAuthenticateData(UserInterface $user): array
    {
        $u2f = $this->getU2f();

        $registration = $this->getRegistration($user);
        if ($registration === null) {
            throw new LocalizedException(__('Missing registration data'));
        }

        return $u2f->getAuthenticateData([$this->hashToObject($registration)]);
    }

    /**
     * Get registration information
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
     * @param UserInterface $user
     * @param array $request
     * @param array $response
     * @return Registration
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Error
     */
    public function registerDevice(UserInterface $user, array $request, array $response): Registration
    {
        // Must convert to object
        $request = $this->hashToObject($request);
        $response = $this->hashToObject($response);

        $u2f = $this->getU2f();
        $res = $u2f->doRegister($request, $response);

        $this->userConfigManager->addProviderConfig((int) $user->getId(), static::CODE, [
            'registration' => [
                'certificate' => $res->certificate,
                'keyHandle' => $res->keyHandle,
                'publicKey' => $res->publicKey,
                'counter' => $res->counter,
            ]
        ]);
        $this->userConfigManager->activateProviderConfiguration((int) $user->getId(), static::CODE);

        return $res;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return true;
    }

    /**
     * @return U2F
     * @throws LocalizedException
     * @throws Error
     */
    private function getU2f(): U2F
    {
        /** @var Store $store */
        $store = $this->storeManager->getStore(Store::ADMIN_CODE);

        $baseUrl = $store->getBaseUrl();
        if (preg_match('/^(https?:\/\/.+?)\//', $baseUrl, $matches)) {
            $domain = $matches[1];
        } else {
            throw new LocalizedException(__('Unexpected error while parsing domain name'));
        }

        /** @var U2F $u2f */
        // @codingStandardsIgnoreStart
        return new U2F($domain);
        // @codingStandardsIgnoreEnd
    }
}
