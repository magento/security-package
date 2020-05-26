<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\TestFramework\TestCase;

use Magento\TestFramework\TestCase\AbstractBackendController as Base;

/**
 * Adds the ability to perform tests without anonymous user.
 */
class AbstractBackendController extends Base
{
    /**
     * @inheritDoc
     *
     * @throws \Magento\Framework\Exception\AuthenticationException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->_objectManager->get(\Magento\Backend\Model\UrlInterface::class)->turnOffSecretKey();
        $this->_auth = $this->_objectManager->get(\Magento\Backend\Model\Auth::class);
        $this->_session = $this->_auth->getAuthStorage();
        $this->login();
    }

    /**
     * Perform logout
     *
     * @return void
     */
    protected function logout(): void
    {
        $this->_auth->getAuthStorage()->destroy(['send_expire_cookie' => false]);
    }

    /**
     * Login as default admin user.
     *
     * @return void
     */
    protected function login(): void
    {
        $credentials = $this->_getAdminCredentials();
        $this->_auth->login($credentials['user'], $credentials['password']);
        /** @var \Magento\Security\Model\Plugin\Auth $authPlugin */
        $authPlugin = $this->_objectManager->get(\Magento\Security\Model\Plugin\Auth::class);
        $authPlugin->afterLogin($this->_auth);
    }

    /**
     * @inheritDoc
     */
    public function dispatch($uri)
    {
        if ($this->getRequest()->getParam('tfa_enabled') === null) {
            $this->getRequest()->setParam('tfa_enabled', true);
        }

        parent::dispatch($uri);
    }
}
