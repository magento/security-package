<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\Google;

use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\TwoFactorAuth\Api\Data\GoogleConfigureInterface as GoogleConfigurationData;
use Magento\TwoFactorAuth\Api\GoogleConfigureInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\UserConfigTokenManagerInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Google;
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
     * @param ConfigurationDataFactory $configurationDataFactory
     * @param Google $google
     * @param UserConfigTokenManagerInterface $tokenManager
     * @param User $userResource
     * @param UserFactory $userFactory
     * @param TfaInterface $tfa
     */
    public function __construct(
        ConfigurationDataFactory $configurationDataFactory,
        Google $google,
        UserConfigTokenManagerInterface $tokenManager,
        User $userResource,
        UserFactory $userFactory,
        TfaInterface $tfa
    ) {
        $this->configurationDataFactory = $configurationDataFactory;
        $this->google = $google;
        $this->tokenManager = $tokenManager;
        $this->userResource = $userResource;
        $this->userFactory = $userFactory;
        $this->tfa = $tfa;
    }

    /**
     * @inheritDoc
     */
    public function getConfigurationData(int $userId, string $tfat): GoogleConfigurationData
    {
        if (!$this->tfa->getProviderIsAllowed($userId, Google::CODE)) {
            throw new WebApiException(__('Provider is not allowed.'));
        }

        if (!$this->tokenManager->isValidFor($userId, $tfat)) {
            throw new AuthorizationException(
                __('Invalid tfat token')
            );
        }
        $user = $this->userFactory->create();
        $this->userResource->load($user, $userId);

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
}
