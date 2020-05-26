<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\Authy;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\User\Api\Data\UserInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Authy;

/**
 * Authy token manager
 */
class Token
{
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
     * @var Json
     */
    private $json;

    /**
     * @param UserConfigManagerInterface $userConfigManager
     * @param Service $service
     * @param CurlFactory $curlFactory
     * @param Json $json
     */
    public function __construct(
        UserConfigManagerInterface $userConfigManager,
        Service $service,
        CurlFactory $curlFactory,
        Json $json
    ) {
        $this->userConfigManager = $userConfigManager;
        $this->curlFactory = $curlFactory;
        $this->service = $service;
        $this->json = $json;
    }

    /**
     * Request a token
     *
     * @param UserInterface $user
     * @param string $via
     * @throws LocalizedException
     */
    public function request(UserInterface $user, string $via): void
    {
        if (!in_array($via, ['call', 'sms'])) {
            throw new LocalizedException(__('Unsupported via method'));
        }

        $providerInfo = $this->userConfigManager->getProviderConfig((int) $user->getId(), Authy::CODE);
        if (!isset($providerInfo['user'])) {
            throw new LocalizedException(__('Missing user information'));
        }

        $url = $this->service->getProtectedApiEndpoint('' . $via . '/' . $providerInfo['user']) . '?force=true';

        $curl = $this->curlFactory->create();
        $curl->addHeader('X-Authy-API-Key', $this->service->getApiKey());
        $curl->get($url);

        $response = $this->json->unserialize($curl->getBody());

        $errorMessage = $this->service->getErrorFromResponse($response);
        if ($errorMessage) {
            throw new LocalizedException(__($errorMessage));
        }
    }

    /**
     * Return true on token validation
     *
     * @param UserInterface $user
     * @param DataObject $request
     * @return bool
     * @throws LocalizedException
     */
    public function verify(UserInterface $user, DataObject $request): bool
    {
        $code = $request->getData('tfa_code');
        if (!preg_match('/^\w+$/', $code)) {
            throw new LocalizedException(__('Invalid code format'));
        }

        $providerInfo = $this->userConfigManager->getProviderConfig((int) $user->getId(), Authy::CODE);
        if (!isset($providerInfo['user'])) {
            throw new LocalizedException(__('Missing user information'));
        }

        $url = $this->service->getProtectedApiEndpoint('verify/' . $code . '/' . $providerInfo['user']);

        $curl = $this->curlFactory->create();
        $curl->addHeader('X-Authy-API-Key', $this->service->getApiKey());
        $curl->get($url);

        $response = $this->json->unserialize($curl->getBody());

        $errorMessage = $this->service->getErrorFromResponse($response);
        if ($errorMessage) {
            throw new LocalizedException(__($errorMessage));
        }

        return true;
    }
}
