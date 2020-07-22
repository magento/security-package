<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\TestFramework\TestCase;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TwoFactorAuth\Api\UserConfigTokenManagerInterface;

/**
 * Base for 2FA configuration related controller tests.
 */
class AbstractConfigureBackendController extends AbstractBackendController
{
    /**
     * @inheritDoc
     */
    protected $expectedNoAccessResponseCode = 302;

    /**
     * @inheritDoc
     */
    protected $resource = 'Magento_Backend::admin';

    /**
     * @var UserConfigTokenManagerInterface
     */
    protected $tokenManager;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->tokenManager = Bootstrap::getObjectManager()->get(UserConfigTokenManagerInterface::class);
    }

    /**
     * Verify that even with ACL an admin user needs token to access the page.
     *
     * @return void
     */
    public function testTokenAccess(): void
    {
        $this->getRequest()->setMethod($this->httpMethod);
        $this->dispatch($this->uri);
        $this->assertRedirect($this->stringContains('login'));
    }

    /**
     * Check that a user with proper token and rights can access the page.
     */
    public function testAclHasAccess()
    {
        $this->getRequest()
            ->setParam('tfat', $this->tokenManager->issueFor((int)$this->_session->getUser()->getId()));

        parent::testAclHasAccess();
    }

    /**
     * Check that a user with proper token but without rights cannot access the page.
     */
    public function testAclNoAccess()
    {
        $this->getRequest()
            ->setParam('tfat', $this->tokenManager->issueFor((int)$this->_session->getUser()->getId()));

        parent::testAclNoAccess();
    }
}
