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
use Magento\TwoFactorAuth\Model\UserAuthenticator;
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
     * @param Google $google
     * @param TfaInterface $tfa
     * @param DataObjectFactory $dataObjectFactory
     * @param AlertInterface $alert
     * @param TokenModelFactory $tokenFactory
     * @param UserAuthenticator $userAuthenticator
     */
    public function __construct(
        Google $google,
        TfaInterface $tfa,
        DataObjectFactory $dataObjectFactory,
        AlertInterface $alert,
        TokenModelFactory $tokenFactory,
        UserAuthenticator $userAuthenticator
    ) {
        $this->google = $google;
        $this->tfa = $tfa;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->alert = $alert;
        $this->tokenFactory = $tokenFactory;
        $this->userAuthenticator = $userAuthenticator;
    }

    /**
     * Get an admin token by authenticating using google
     *
     * @param string $username
     * @param string $password
     * @param string $otp
     * @return string
     * @throws AuthorizationException
     * @throws WebApiException
     */
    public function getToken(string $username, string $password, string $otp): string
    {
        $user = $this->userAuthenticator->authenticateWithCredentials($username, $password);
        $userId = (int)$user->getId();

        if (!$this->tfa->getProviderIsAllowed($userId, Google::CODE)) {
            throw new WebApiException(__('Provider is not allowed.'));
        }

        if (!$this->tfa->getProviderByCode(Google::CODE)->isActive($userId)) {
            throw new WebApiException(__('Provider is not configured.'));
        }

        if ($this->google->verify($user, $this->dataObjectFactory->create([
                'data' => [
                    'tfa_code' => $otp
                ],
            ]))
        ) {
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
