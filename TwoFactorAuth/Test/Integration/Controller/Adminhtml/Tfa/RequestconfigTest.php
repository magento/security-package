<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Integration\Controller\Adminhtml\Tfa;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TwoFactorAuth\TestFramework\TestCase\AbstractBackendController;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\UserConfigTokenManagerInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Google;
use Magento\Backend\Model\Auth\Session;

/**
 * Testing the controller for the page that requests 2FA config from users.
 *
 * @magentoAppArea adminhtml
 * @magentoDbIsolation enabled
 */
class RequestconfigTest extends AbstractBackendController
{
    /**
     * @inheritDoc
     */
    protected $uri = 'backend/tfa/tfa/requestconfig';

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var UserConfigTokenManagerInterface
     */
    private $tokenManager;

    /**
     * @var Session
     */
    private $session;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->tfa = Bootstrap::getObjectManager()->get(TfaInterface::class);
        $this->tokenManager = Bootstrap::getObjectManager()->get(UserConfigTokenManagerInterface::class);
        $this->session = Bootstrap::getObjectManager()->get(Session::class);
    }

    /**
     * Verify that 2FA config request is display for users when 2FA is not configured for the app.
     *
     * @return void
     */
    public function testAppConfigRequested(): void
    {
        $this->dispatch($this->uri);
        self::assertMatchesRegularExpression(
            '/You need to configure Two\-Factor Authorization/',
            $this->getResponse()->getBody()
        );
    }

    /**
     * Verify that 2FA config request is display for users when 2FA is not configured for the user.
     *
     * @return void
     * @magentoConfigFixture default/twofactorauth/general/force_providers google
     */
    public function testUserConfigRequested(): void
    {
        $this->dispatch($this->uri);
        self::assertMatchesRegularExpression(
            '/You need to configure Two\-Factor Authorization/',
            $this->getResponse()->getBody()
        );
    }

    /**
     * Verify that 2FA config is not requested when 2FA is configured.
     *
     * @return void
     * @magentoConfigFixture default/twofactorauth/general/force_providers google
     * @magentoDbIsolation enabled
     */
    public function testNotRequested(): void
    {
        $this->expectException(\Magento\Framework\Exception\AuthorizationException::class);
        $this->tfa->getProvider(Google::CODE)->activate((int)$this->_session->getUser()->getId());
        $this->dispatch($this->uri);
    }

    /**
     * Verify that users with valid tokens get redirected to the app 2FA config page.
     *
     * @return void
     */
    public function testRedirectToAppConfig(): void
    {
        $this->getRequest()
            ->setQueryValue('tfat', $this->tokenManager->issueFor((int)$this->_session->getUser()->getId()));
        $this->dispatch($this->uri);
        $this->assertRedirect($this->stringContains('tfa/configure'));
    }

    /**
     * Verify that users with valid tokens get redirected to the user 2FA config page.
     *
     * @return void
     * @magentoConfigFixture default/twofactorauth/general/force_providers google
     */
    public function testRedirectToUserConfig(): void
    {
        $this->getRequest()
            ->setQueryValue('tfat', $this->tokenManager->issueFor((int)$this->_session->getUser()->getId()));
        $this->dispatch($this->uri);
        $this->assertRedirect($this->stringContains('tfa/index'));
    }

    /**
     * Verify that session flag is set when 2FA config email is sent to the user.
     *
     * @return void
     * @magentoConfigFixture default/twofactorauth/general/force_providers google
     */
    public function testUserConfigRequestedFlag(): void
    {
        $this->assertNull($this->session->getData('tfa_email_sent'));
        $this->dispatch($this->uri);
        self::assertMatchesRegularExpression(
            '/You need to configure Two\-Factor Authorization/',
            $this->getResponse()->getBody()
        );
        $this->assertTrue($this->session->getData('tfa_email_sent'));
    }
}
