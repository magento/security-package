<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\Authy;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;
use Magento\User\Api\Data\UserInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Authy;

/**
 * Authy One Touch manager class
 */
class OneTouch
{
    /**
     * Configuration XML path for one touch message
     */
    public const XML_PATH_ONETOUCH_MESSAGE = 'twofactorauth/authy/onetouch_message';

    /**
     * @var CurlFactory
     */
    private $curlFactory;

    /**
     * @var UserConfigManagerInterface
     */
    private $userConfigManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Service
     */
    private $service;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Json
     */
    private $json;

    /**
     * OneTouch constructor.
     *
     * @param CurlFactory $curlFactory
     * @param UserConfigManagerInterface $userConfigManager
     * @param Service $service
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param Json $json
     */
    public function __construct(
        CurlFactory $curlFactory,
        UserConfigManagerInterface $userConfigManager,
        Service $service,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        Json $json
    ) {
        $this->curlFactory = $curlFactory;
        $this->userConfigManager = $userConfigManager;
        $this->storeManager = $storeManager;
        $this->service = $service;
        $this->scopeConfig = $scopeConfig;
        $this->json = $json;
    }

    /**
     * Request one-touch
     *
     * @param UserInterface $user
     * @throws LocalizedException
     */
    public function request(UserInterface $user): void
    {
        $providerInfo = $this->userConfigManager->getProviderConfig((int) $user->getId(), Authy::CODE);
        if (!isset($providerInfo['user'])) {
            throw new LocalizedException(__('Missing user information'));
        }

        $url = $this->service->getOneTouchApiEndpoint('users/' . $providerInfo['user'] . '/approval_requests');

        $curl = $this->curlFactory->create();
        $curl->addHeader('X-Authy-API-Key', $this->service->getApiKey());
        $curl->post($url, [
            'message' => $this->scopeConfig->getValue(self::XML_PATH_ONETOUCH_MESSAGE),
            'details[URL]' => $this->storeManager->getStore()->getBaseUrl(),
            'details[User]' => $user->getUserName(),
            'details[Email]' => $user->getEmail(),
            'seconds_to_expire' => 300,
        ]);

        $response = $this->json->unserialize($curl->getBody());

        $errorMessage = $this->service->getErrorFromResponse($response);
        if ($errorMessage) {
            throw new LocalizedException(__($errorMessage));
        }

        $this->userConfigManager->addProviderConfig((int) $user->getId(), Authy::CODE, [
            'pending_approval' => $response['approval_request']['uuid'],
        ]);
    }

    /**
     * Verify one-touch
     *
     * @param UserInterface $user
     * @return string
     * @throws LocalizedException
     */
    public function verify(UserInterface $user): string
    {
        $providerInfo = $this->userConfigManager->getProviderConfig((int) $user->getId(), Authy::CODE);
        if (!isset($providerInfo['user'])) {
            throw new LocalizedException(__('Missing user information'));
        }

        if (!isset($providerInfo['pending_approval'])) {
            throw new LocalizedException(__('No approval requests for this user'));
        }

        $approvalCode = $providerInfo['pending_approval'];

        if (!preg_match('/^\w[\w\-]+\w$/', $approvalCode)) {
            throw new LocalizedException(__('Invalid approval code'));
        }

        $url = $this->service->getOneTouchApiEndpoint('approval_requests/' . $approvalCode);

        $times = 10;

        for ($i=0; $i<$times; $i++) {
            $curl = $this->curlFactory->create();
            $curl->addHeader('X-Authy-API-Key', $this->service->getApiKey());
            $curl->get($url);

            $response = $this->json->unserialize($curl->getBody());

            $errorMessage = $this->service->getErrorFromResponse($response);
            if ($errorMessage) {
                throw new LocalizedException(__($errorMessage));
            }

            $status = $response['approval_request']['status'];
            if ($status === 'pending') {
                // @codingStandardsIgnoreStart
                sleep(1); // I know... but it is the only option I have here
                // @codingStandardsIgnoreEnd
                continue;
            }

            if ($status === 'approved') {
                return $status;
            }

            return $status;
        }

        return 'retry';
    }
}
