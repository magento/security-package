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
use Magento\TwoFactorAuth\Model\AlertInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Google;
use Magento\TwoFactorAuth\Model\Data\Provider\Engine\Google\ConfigurationDataFactory;
use Magento\TwoFactorAuth\Model\UserAuthenticator;

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
     * @var UserAuthenticator
     */
    private $userAuthenticator;

    /**
     * @param ConfigurationDataFactory $configurationDataFactory
     * @param Google $google
     * @param TfaInterface $tfa
     * @param DataObjectFactory $dataObjectFactory
     * @param AlertInterface $alert
     * @param TokenModelFactory $tokenFactory
     * @param UserAuthenticator $userAuthenticator
     */
    public function __construct(
        ConfigurationDataFactory $configurationDataFactory,
        Google $google,
        TfaInterface $tfa,
        DataObjectFactory $dataObjectFactory,
        AlertInterface $alert,
        TokenModelFactory $tokenFactory,
        UserAuthenticator $userAuthenticator
    ) {
        $this->configurationDataFactory = $configurationDataFactory;
        $this->google = $google;
        $this->tfa = $tfa;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->alert = $alert;
        $this->tokenFactory = $tokenFactory;
        $this->userAuthenticator = $userAuthenticator;
    }

    /**
     * @inheritDoc
     */
    public function getConfigurationData(int $userId, string $tfaToken): GoogleConfigurationData
    {
        $user = $this->userAuthenticator->authenticateWithTokenAndProvider($userId, $tfaToken, Google::CODE);

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
        $user = $this->userAuthenticator->authenticateWithTokenAndProvider($userId, $tfaToken, Google::CODE);

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
}
