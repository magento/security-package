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
use Magento\TwoFactorAuth\Api\GoogleAuthenticateInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Model\AlertInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Google;
use Magento\User\Model\ResourceModel\User;
use Magento\User\Model\UserFactory;
use Magento\Integration\Model\Oauth\TokenFactory as TokenModelFactory;

/**
 * Authenticate with google provider
 */
class Authenticate implements GoogleAuthenticateInterface
{
    /**
     * @var Google
     */
    private $google;

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
     * @param Google $google
     * @param User $userResource
     * @param UserFactory $userFactory
     * @param TfaInterface $tfa
     * @param DataObjectFactory $dataObjectFactory
     * @param AlertInterface $alert
     * @param TokenModelFactory $tokenFactory
     */
    public function __construct(
        Google $google,
        User $userResource,
        UserFactory $userFactory,
        TfaInterface $tfa,
        DataObjectFactory $dataObjectFactory,
        AlertInterface $alert,
        TokenModelFactory $tokenFactory
    ) {
        $this->google = $google;
        $this->userResource = $userResource;
        $this->userFactory = $userFactory;
        $this->tfa = $tfa;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->alert = $alert;
        $this->tokenFactory = $tokenFactory;
    }

    /**
     * Get an admin token by authenticating using google
     *
     * @param int $userId
     * @param string $otp
     * @return string
     */
    public function getToken(int $userId, string $otp): string
    {
        if (!$this->tfa->getProviderIsAllowed($userId, Google::CODE)) {
            throw new WebApiException(__('Provider is not allowed.'));
        }

        $user = $this->userFactory->create();
        $this->userResource->load($user, $userId);

        if ($this->google->verify($user, $this->dataObjectFactory->create([
                'data' => [
                    'tfa_code' => $otp
                ],
            ]))
        ) {
            if ($this->tfa->getProvider(Google::CODE)->isActive($userId)) {
                $this->tfa->getProvider(Google::CODE)->activate((int)$user->getId());
            }

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
