<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\Google;

use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\Integration\Model\Oauth\TokenFactory as TokenModelFactory;
use Magento\TwoFactorAuth\Api\Data\GoogleConfigureInterface as GoogleConfigurationData;
use Magento\TwoFactorAuth\Api\GoogleConfigureInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\UserConfigTokenManagerInterface;
use Magento\TwoFactorAuth\Model\AlertInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Google;
use Magento\TwoFactorAuth\Model\Data\Provider\Engine\Google\ConfigurationDataFactory;
use Magento\User\Api\Data\UserInterface;
use Magento\User\Model\ResourceModel\User;
use Magento\User\Model\UserFactory;

/**
 * Configure google provider
 */
class Configure implements GoogleConfigureInterface
{
    /**
     * @var ConfigurationDataFactory
     */
    private $configurationDataFactory;

    /**
     * @var Google
     */
    private $google;

    /**
     * @var UserConfigTokenManagerInterface
     */
    private $tokenManager;

    /**
     * @var User
     */
    private $userFactory;

    /**
     * @var User
     */
    private $userResource;

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var AlertInterface
     */
    private $alert;

    /**
     * @var TokenModelFactory
     */
    private $tokenFactory;

    /**
     * @param ConfigurationDataFactory $configurationDataFactory
     * @param Google $google
     * @param UserConfigTokenManagerInterface $tokenManager
     * @param User $userResource
     * @param UserFactory $userFactory
     * @param TfaInterface $tfa
     * @param DataObjectFactory $dataObjectFactory
     * @param AlertInterface $alert
     * @param TokenModelFactory $tokenFactory
     */
    public function __construct(
        ConfigurationDataFactory $configurationDataFactory,
        Google $google,
        UserConfigTokenManagerInterface $tokenManager,
        User $userResource,
        UserFactory $userFactory,
        TfaInterface $tfa,
        DataObjectFactory $dataObjectFactory,
        AlertInterface $alert,
        TokenModelFactory $tokenFactory
    ) {
        $this->configurationDataFactory = $configurationDataFactory;
        $this->google = $google;
        $this->tokenManager = $tokenManager;
        $this->userResource = $userResource;
        $this->userFactory = $userFactory;
        $this->tfa = $tfa;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->alert = $alert;
        $this->tokenFactory = $tokenFactory;
    }

    /**
     * @inheritDoc
     */
    public function getConfigurationData(int $userId, string $tfaToken): GoogleConfigurationData
    {
        $user = $this->validateAndGetUser($userId, $tfaToken);

        return $this->configurationDataFactory->create(
            [
                'data' => [
                    GoogleConfigurationData::QR_CODE_URL =>
                        'data:image/png;base64,' . base64_encode($this->google->getQrCodeAsPng($user)),
                    GoogleConfigurationData::SECRET_CODE => $this->google->getSecretCode($user)
                ]
            ]
        );
    }

    /**
     * Activate the provider
     *
     * @param int $userId
     * @param string $tfaToken
     * @param string $otp
     * @return string
     * @throws AuthorizationException
     * @throws WebApiException
     */
    public function activate(int $userId, string $tfaToken, string $otp): string
    {
        $user = $this->validateAndGetUser($userId, $tfaToken);

        if ($this->google->verify($user, $this->dataObjectFactory->create([
            'data' => [
                'tfa_code' => $otp
            ],
        ]))
        ) {
            $this->tfa->getProvider(Google::CODE)->activate((int)$user->getId());

            $this->alert->event(
                'Magento_TwoFactorAuth',
                'New Google Authenticator code issued',
                AlertInterface::LEVEL_INFO,
                $user->getUserName()
            );

            return $this->tokenFactory->create()->createAdminToken($userId)->getToken();
        } else {
            throw new AuthorizationException(__('Invalid code.'));
        }
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
        if (!$this->tfa->getProviderIsAllowed($userId, Google::CODE)) {
            throw new WebApiException(__('Provider is not allowed.'));
        } elseif ($this->tfa->getProviderByCode(Google::CODE)->isActive($userId)) {
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
