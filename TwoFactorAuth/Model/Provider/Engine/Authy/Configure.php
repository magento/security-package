<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\Authy;

use Magento\TwoFactorAuth\Api\AuthyConfigureInterface;
use Magento\TwoFactorAuth\Api\Data\AuthyDeviceInterface;
use Magento\TwoFactorAuth\Model\AlertInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Authy;
use Magento\TwoFactorAuth\Api\Data\AuthyRegistrationPromptResponseInterfaceFactory as ResponseFactory;
use Magento\TwoFactorAuth\Api\Data\AuthyRegistrationPromptResponseInterface as ResponseInterface;
use Magento\TwoFactorAuth\Model\UserAuthenticator;

/**
 * Configures authy
 */
class Configure implements AuthyConfigureInterface
{
    /**
     * @var AlertInterface
     */
    private $alert;

    /**
     * @var Verification
     */
    private $verification;

    /**
     * @var AuthyRegistrationPromptResponseInterfaceFactory
     */
    private $responseFactory;

    /**
     * @var Authy
     */
    private $authy;

    /**
     * @var UserAuthenticator
     */
    private $userAuthenticator;

    /**
     * @param AlertInterface $alert
     * @param Verification $verification
     * @param ResponseFactory $responseFactory
     * @param Authy $authy
     * @param UserAuthenticator $userAuthenticator
     */
    public function __construct(
        AlertInterface $alert,
        Verification $verification,
        ResponseFactory $responseFactory,
        Authy $authy,
        UserAuthenticator $userAuthenticator
    ) {
        $this->alert = $alert;
        $this->verification = $verification;
        $this->responseFactory = $responseFactory;
        $this->authy = $authy;
        $this->userAuthenticator = $userAuthenticator;
    }

    /**
     * @inheritDoc
     */
    public function sendDeviceRegistrationPrompt(
        string $tfaToken,
        AuthyDeviceInterface
        $deviceData
    ): ResponseInterface {
        $user = $this->userAuthenticator->authenticateWithTokenAndProvider($tfaToken, Authy::CODE);

        $response = [];
        $this->verification->request(
            $user,
            $deviceData->getCountry(),
            $deviceData->getPhoneNumber(),
            $deviceData->getMethod(),
            $response
        );

        $this->alert->event(
            'Magento_TwoFactorAuth',
            'New authy verification request via ' . $deviceData->getMethod(),
            AlertInterface::LEVEL_INFO,
            $user->getUserName()
        );

        return $this->responseFactory->create(
            [
                'data' => [
                    ResponseInterface::MESSAGE => $response['message'],
                    ResponseInterface::EXPIRATION_SECONDS => (int)$response['seconds_to_expire'],
                ]
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function activate(string $tfaToken, string $otp): void
    {
        $user = $this->userAuthenticator->authenticateWithTokenAndProvider($tfaToken, Authy::CODE);

        try {
            $this->verification->verify($user, $otp);
            $this->authy->enroll($user);

            $this->alert->event(
                'Magento_TwoFactorAuth',
                'Authy identity verified',
                AlertInterface::LEVEL_INFO,
                $user->getUserName()
            );
        } catch (\Throwable $e) {
            $this->alert->event(
                'Magento_TwoFactorAuth',
                'Authy identity verification failure',
                AlertInterface::LEVEL_ERROR,
                $user->getUserName(),
                $e->getMessage()
            );

            throw $e;
        }
    }
}
