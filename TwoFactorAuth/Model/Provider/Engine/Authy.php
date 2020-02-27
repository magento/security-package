<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\User\Api\Data\UserInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use Magento\TwoFactorAuth\Api\EngineInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Authy\Service;
use Magento\TwoFactorAuth\Model\Provider\Engine\Authy\Token;
use Zend\Json\Json;

/**
 * Authy engine
 */
class Authy implements EngineInterface
{
    /**
     * Must be the same as defined in di.xml
     */
    public const CODE = 'authy';

    /**
     * Configuration XML path for enabled flag
     *
     * @deprecated Providers are now enabled via "forced_providers" config
     */
    public const XML_PATH_ENABLED = 'twofactorauth/authy/enabled';

    /**
     * Configuration XML path to allow trusted devices
     *
     * @deprecated Trusted devices functionality is now deprecated
     */
    public const XML_PATH_ALLOW_TRUSTED_DEVICES = 'twofactorauth/authy/allow_trusted_devices';

    /**
     * @var UserConfigManagerInterface
     */
    private $userConfigManager;

    /**
     * @var CurlFactory
     */
    private $curlFactory;

    /**
     * @var Service
     */
    private $service;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Token
     */
    private $token;

    /**
     * @param UserConfigManagerInterface $userConfigManager
     * @param ScopeConfigInterface $scopeConfig
     * @param Token $token
     * @param Service $service
     * @param CurlFactory $curlFactory
     */
    public function __construct(
        UserConfigManagerInterface $userConfigManager,
        ScopeConfigInterface $scopeConfig,
        Token $token,
        Service $service,
        CurlFactory $curlFactory
    ) {
        $this->userConfigManager = $userConfigManager;
        $this->curlFactory = $curlFactory;
        $this->service = $service;
        $this->scopeConfig = $scopeConfig;
        $this->token = $token;
    }

    /**
     * Enroll in Authy
     * @param UserInterface $user
     * @return bool
     * @throws LocalizedException
     */
    public function enroll(UserInterface $user): bool
    {
        $providerInfo = $this->userConfigManager->getProviderConfig((int) $user->getId(), Authy::CODE);
        if (!isset($providerInfo['country_code'])) {
            throw new LocalizedException(__('Missing phone information'));
        }

        $url = $this->service->getProtectedApiEndpoint('users/new');
        $curl = $this->curlFactory->create();

        $curl->addHeader('X-Authy-API-Key', $this->service->getApiKey());
        $curl->post($url, [
            'user[email]' => $user->getEmail(),
            'user[cellphone]' => $providerInfo['phone_number'],
            'user[country_code]' => $providerInfo['country_code'],
        ]);

        $response = Json::decode($curl->getBody(), Json::TYPE_ARRAY);

        $errorMessage = $this->service->getErrorFromResponse($response);
        if ($errorMessage) {
            throw new LocalizedException(__($errorMessage));
        }

        $this->userConfigManager->addProviderConfig((int) $user->getId(), Authy::CODE, [
            'user' => $response['user']['id'],
        ]);

        $this->userConfigManager->activateProviderConfiguration((int) $user->getId(), Authy::CODE);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        try {
            return !!$this->service->getApiKey();
        } catch (\TypeError $exception) {
            //API key is empty, returned null instead of a string
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function verify(UserInterface $user, DataObject $request): bool
    {
        return $this->token->verify($user, $request);
    }

    /**
     * @inheritDoc
     */
    public function isTrustedDevicesAllowed(): bool
    {
        return false;
    }
}
