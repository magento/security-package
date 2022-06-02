<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Test\Unit\Plugin\Customer;

use Magento\Customer\ViewModel\ForgotPasswordButton;
use Magento\ReCaptchaCustomer\Plugin\Customer\DisableForgotPasswordButton;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test disable Forgot password button while captcha is loading
 */
class DisableForgotPasswordButtonTest extends TestCase
{
    /**
     * @var IsCaptchaEnabledInterface|MockObject
     */
    protected $isCaptchaEnabled;

    /**
     * @var ForgotPasswordButton|MockObject
     */
    protected $subject;

    /**
     * @var DisableForgotPasswordButton
     */
    protected $plugin;

    protected function setUp(): void
    {
        $this->isCaptchaEnabled = $this->getMockForAbstractClass(
            IsCaptchaEnabledInterface::class
        );
        $this->subject = $this->createMock(ForgotPasswordButton::class);

        $this->plugin = new DisableForgotPasswordButton(
            $this->isCaptchaEnabled
        );
    }

    public function testAfterEnabled()
    {
        $key = 'customer_forgot_password';
        $this->isCaptchaEnabled->expects($this->once())
            ->method('isCaptchaEnabledFor')->with($key)->willReturn(true);
        $this->assertEquals(true, $this->plugin->afterDisabled($this->subject));
    }
}
