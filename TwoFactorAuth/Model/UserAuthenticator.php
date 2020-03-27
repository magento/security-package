<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model;

use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\Integration\Model\CredentialsValidator;
use Magento\Integration\Model\Oauth\Token\RequestThrottler;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\UserConfigTokenManagerInterface;
use Magento\User\Model\ResourceModel\User as UserResource;
use Magento\User\Model\User;
use Magento\User\Model\UserFactory;

/**
 * Retrieves users from credentials and enforced throttling
 */
class UserAuthenticator
{
    /**
     * @var CredentialsValidator
     */
    private $credentialsValidator;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var RequestThrottler
     */
    private $requestThrottler;

    /**
     * @var UserResource
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
     * @var UserConfigTokenManagerInterface
     */
    private $tokenManager;

    /**
     * @param CredentialsValidator $credentialsValidator
     * @param UserFactory $userFactory
     * @param RequestThrottler $requestThrottler
     * @param UserResource $userResource
     * @param UserConfigTokenManagerInterface $tokenManager
     * @param TfaInterface $tfa
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        CredentialsValidator $credentialsValidator,
        UserFactory $userFactory,
        RequestThrottler $requestThrottler,
        UserResource $userResource,
        UserConfigTokenManagerInterface $tokenManager,
        TfaInterface $tfa,
        DataObjectFactory $dataObjectFactory
    ) {
        $this->credentialsValidator = $credentialsValidator;
        $this->userFactory = $userFactory;
        $this->requestThrottler = $requestThrottler;
        $this->userResource = $userResource;
        $this->tfa = $tfa;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->tokenManager = $tokenManager;
    }

    /**
     * Get a user with credentials while enforcing throttling
     *
     * @param string $username
     * @param string $password
     * @return User
     */
    public function authenticateWithCredentials(string $username, string $password): User
    {
        $this->credentialsValidator->validate($username, $password);
        $this->requestThrottler->throttle($username, RequestThrottler::USER_TYPE_ADMIN);

        $user = $this->userFactory->create();
        $user->login($username, $password);

        if (!$user->getId()) {
            $this->requestThrottler->logAuthenticationFailure($username, RequestThrottler::USER_TYPE_ADMIN);

            throw new AuthenticationException(
                __(
                    'The account sign-in was incorrect or your account is disabled temporarily. '
                    . 'Please wait and try again later.'
                )
            );
        }

        $this->requestThrottler->resetAuthenticationFailuresCount($username, RequestThrottler::USER_TYPE_ADMIN);

        return $user;
    }

    /**
     * Obtain a user with an id and a tfa token
     *
     * @param int $userId
     * @param string $tfaToken
     * @param string $providerCode
     * @return User
     * @throws AuthorizationException
     * @throws WebApiException
     */
    public function authenticateWithTokenAndProvider(int $userId, string $tfaToken, string $providerCode): User
    {
        if (!$this->tfa->getProviderIsAllowed($userId, $providerCode)) {
            throw new WebApiException(__('Provider is not allowed.'));
        } elseif ($this->tfa->getProviderByCode($providerCode)->isActive($userId)) {
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
