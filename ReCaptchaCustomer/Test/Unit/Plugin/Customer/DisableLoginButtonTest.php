<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Test\Unit\Plugin\Customer;

use Magento\Customer\ViewModel\LoginButton;
use Magento\ReCaptchaCustomer\Plugin\Customer\DisableLoginButton;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test disable Login button while captcha is loading
 */
class DisableLoginButtonTest extends TestCase
{
    /**
     * IsCaptcha Enabled mock
     *
     * @var IsCaptchaEnabledInterface|MockObject
     */
    protected $isCaptchaEnabled;

    /**
     * Subject LoginButton
     *
     * @var LoginButton|MockObject
     */
    protected $subject;

    /**
     * Tested plugin
     *
     * @var DisableLoginButtonTest
     */
    protected $plugin;

    protected function setUp(): void
    {
        $this->isCaptchaEnabled = $this->getMockForAbstractClass(
            IsCaptchaEnabledInterface::class
        );
        $this->subject = $this->createMock(LoginButton::class);

        $this->plugin = new DisableLoginButton(
            $this->isCaptchaEnabled
        );
    }

    public function testAfterEnabled()
    {
        $key = 'customer_login';
        $this->isCaptchaEnabled->expects($this->once())
            ->method('isCaptchaEnabledFor')->with($key)->willReturn(true);
        $this->assertEquals(true, $this->plugin->afterDisabled($this->subject));
    }
}
