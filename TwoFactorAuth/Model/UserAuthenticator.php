<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model;

use Magento\Integration\Model\CredentialsValidator;
use Magento\Integration\Model\Oauth\Token\RequestThrottler;
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
     * @param CredentialsValidator $credentialsValidator
     * @param UserFactory $userFactory
     * @param RequestThrottler $requestThrottler
     */
    public function __construct(
        CredentialsValidator $credentialsValidator,
        UserFactory $userFactory,
        RequestThrottler $requestThrottler
    ) {
        $this->credentialsValidator = $credentialsValidator;
        $this->userFactory = $userFactory;
        $this->requestThrottler = $requestThrottler;
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
            throw new \InvalidArgumentException('Invalid credentials');
        }

        return $user;
    }
}
