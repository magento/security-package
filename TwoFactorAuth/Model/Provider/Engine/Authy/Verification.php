<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\Authy;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\User\Api\Data\UserInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Authy;

/**
 * Authy verification management
 */
class Verification
{
    /**
     * @var CurlFactory
     */
    private $curlFactory;

    /**
     * @var Service
     */
    private $service;

    /**
     * @var UserConfigManagerInterface
     */
    private $userConfigManager;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var Json
     */
    private $json;

    /**
     * @param CurlFactory $curlFactory
     * @param DateTime $dateTime
     * @param UserConfigManagerInterface $userConfigManager
     * @param Service $service
     * @param Json $json
     */
    public function __construct(
        CurlFactory $curlFactory,
        DateTime $dateTime,
        UserConfigManagerInterface $userConfigManager,
        Service $service,
        Json $json
    ) {
        $this->curlFactory = $curlFactory;
        $this->service = $service;
        $this->userConfigManager = $userConfigManager;
        $this->dateTime = $dateTime;
        $this->json = $json;
    }

    /**
     * Verify phone number
     *
     * @param UserInterface $user
     * @param string        $country
     * @param string        $phoneNumber
     * @param string        $method
     * @param array        &$response
     * @throws LocalizedException
     */
    public function request(
        UserInterface $user,
        string $country,
        string $phoneNumber,
        string $method,
        array &$response
    ): void {
        $url = $this->service->getProtectedApiEndpoint('phones/verification/start');

        $curl = $this->curlFactory->create();
        $curl->addHeader('X-Authy-API-Key', $this->service->getApiKey());
        $curl->post($url, [
            'via' => $method,
            'country_code' => $country,
            'phone_number' => $phoneNumber
        ]);

        $response = $this->json->unserialize($curl->getBody());

        $errorMessage = $this->service->getErrorFromResponse($response);
        if ($errorMessage) {
            throw new LocalizedException(__($errorMessage));
        }

        $this->userConfigManager->addProviderConfig((int) $user->getId(), Authy::CODE, [
            'country_code' => $country,
            'phone_number' => $phoneNumber,
            'carrier' => $response['carrier'],
            'mobile' => $response['is_cellphone'],
            'verify' => [
                'uuid' => $response['uuid'],
                'via' => $method,
                'expires' => $this->dateTime->timestamp() + $response['seconds_to_expire'],
                'seconds_to_expire' => $response['seconds_to_expire'],
                'message' => $response['message'],
            ],
            'phone_confirmed' => false,
        ]);
    }

    /**
     * Verify phone number
     *
     * @param UserInterface $user
     * @param string $verificationCode
     * @throws LocalizedException
     */
    public function verify(UserInterface $user, string $verificationCode): void
    {
        $providerInfo = $this->userConfigManager->getProviderConfig((int) $user->getId(), Authy::CODE);
        if (!isset($providerInfo['country_code'])) {
            throw new LocalizedException(__('Missing verify request information'));
        }

        $url = $this->service->getProtectedApiEndpoint('phones/verification/check');

        $curl = $this->curlFactory->create();
        $curl->addHeader('X-Authy-API-Key', $this->service->getApiKey());
        $curl->get($url . '?' . http_build_query([
                'country_code' => $providerInfo['country_code'],
                'phone_number' => $providerInfo['phone_number'],
                'verification_code' => $verificationCode,
            ]));

        $response = $this->json->unserialize($curl->getBody());

        $errorMessage = $this->service->getErrorFromResponse($response);
        if ($errorMessage) {
            throw new LocalizedException(__($errorMessage));
        }

        $this->userConfigManager->addProviderConfig((int) $user->getId(), Authy::CODE, [
            'phone_confirmed' => true,
        ]);
        $this->userConfigManager->activateProviderConfiguration((int) $user->getId(), Authy::CODE);
    }
}
