<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\Authy;

use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\TwoFactorAuth\Api\AuthyConfigureInterface;
use Magento\TwoFactorAuth\Api\Data\AuthyDeviceInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\UserConfigTokenManagerInterface;
use Magento\TwoFactorAuth\Model\AlertInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Authy;
use Magento\User\Api\Data\UserInterface;
use Magento\User\Model\ResourceModel\User;
use Magento\User\Model\UserFactory;
use Magento\TwoFactorAuth\Api\Data\AuthyRegistrationPromptResponseInterfaceFactory as ResponseFactory;
use Magento\TwoFactorAuth\Api\Data\AuthyRegistrationPromptResponseInterface as ResponseInterface;

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
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var UserConfigTokenManagerInterface
     */
    private $tokenManager;

    /**
     * @var User
     */
    private $userResource;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var AuthyRegistrationPromptResponseInterfaceFactory
     */
    private $responseFactory;

    /**
     * @param AlertInterface $alert
     * @param Verification $verification
     * @param TfaInterface $tfa
     * @param User $userResource
     * @param UserFactory $userFactory
     * @param ResponseFactory $responseFactory
     * @param UserConfigTokenManagerInterface $tokenManager
     */
    public function __construct(
        AlertInterface $alert,
        Verification $verification,
        TfaInterface $tfa,
        User $userResource,
        UserFactory $userFactory,
        ResponseFactory $responseFactory,
        UserConfigTokenManagerInterface $tokenManager
    ) {
        $this->alert = $alert;
        $this->verification = $verification;
        $this->tfa = $tfa;
        $this->userResource = $userResource;
        $this->userFactory = $userFactory;
        $this->responseFactory = $responseFactory;
        $this->tokenManager = $tokenManager;
    }

    /**
     * @inheritDoc
     */
    public function sendDeviceRegistrationPrompt(
        int $userId,
        string $tfaToken,
        AuthyDeviceInterface $deviceData
    ): ResponseInterface {
        $user = $this->validateAndGetUser($userId, $tfaToken);

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
     * Activate the provider and get an admin token
     *
     * @param int $userId
     * @param string $tfaToken
     * @param string $otp
     * @return string
     */
    public function activate(int $userId, string $tfaToken, string $otp): string
    {
        return 'foo';
    }

    /**
     * Validate input params and get a user
     *
     * @param int $userId
     * @param string $tfaToken
     * @return UserInterface
     * @throws AuthorizationException
     * @throws WebApiException
     */
    private function validateAndGetUser(int $userId, string $tfaToken): UserInterface
    {
        if (!$this->tfa->getProviderIsAllowed($userId, Authy::CODE)) {
            throw new WebApiException(__('Provider is not allowed.'));
        } elseif ($this->tfa->getProviderByCode(Authy::CODE)->isActive($userId)) {
            throw new WebApiException(__('Provider is already configured.'));
        } elseif (!$this->tokenManager->isValidFor($userId, $tfaToken)) {
            throw new AuthorizationException(
                __('Invalid tfa token')
            );
        }

        $user = $this->userFactory->create();
        $this->userResource->load($user, $userId);

        return $user;
    }
}
