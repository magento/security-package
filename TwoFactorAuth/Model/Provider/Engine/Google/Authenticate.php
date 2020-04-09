<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\Google;

use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\Integration\Api\AdminTokenServiceInterface;
use Magento\TwoFactorAuth\Api\GoogleAuthenticateInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Model\AlertInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Google;
use Magento\User\Model\UserFactory;

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
     * @var AdminTokenServiceInterface
     */
    private $adminTokenService;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @param Google $google
     * @param TfaInterface $tfa
     * @param DataObjectFactory $dataObjectFactory
     * @param AlertInterface $alert
     * @param AdminTokenServiceInterface $adminTokenService
     * @param UserFactory $userFactory
     */
    public function __construct(
        Google $google,
        TfaInterface $tfa,
        DataObjectFactory $dataObjectFactory,
        AlertInterface $alert,
        AdminTokenServiceInterface $adminTokenService,
        UserFactory $userFactory
    ) {
        $this->google = $google;
        $this->tfa = $tfa;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->alert = $alert;
        $this->adminTokenService = $adminTokenService;
        $this->userFactory = $userFactory;
    }

    /**
     * @inheritDoc
     */
    public function getToken(string $username, string $password, string $otp): string
    {
        $user = $this->userFactory->create();
        $user->loadByUsername($username);
        $userId = (int)$user->getId();
        if ($userId === 0) {
            throw new AuthenticationException(__(
                'The account sign-in was incorrect or your account is disabled temporarily. '
                . 'Please wait and try again later.'
            ));
        }

        $token = $this->adminTokenService->createAdminAccessToken($username, $password);

        if (!$this->tfa->getProviderIsAllowed($userId, Google::CODE)) {
            throw new WebApiException(__('Provider is not allowed.'));
        } elseif (!$this->tfa->getProviderByCode(Google::CODE)->isActive($userId)) {
            throw new WebApiException(__('Provider is not configured.'));
        } elseif ($this->google->verify($user, $this->dataObjectFactory->create([
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

            return $token;
        } else {
            throw new AuthorizationException(__('Invalid code.'));
        }
    }
}
