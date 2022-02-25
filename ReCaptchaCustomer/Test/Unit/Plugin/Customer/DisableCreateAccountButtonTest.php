<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Test\Unit\Plugin\Customer;

use Magento\Customer\ViewModel\CreateAccountButton;
use Magento\ReCaptchaCustomer\Plugin\Customer\DisableCreateAccountButton;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test disable Login button while captcha is loading
 */
class DisableCreateAccountButtonTest extends TestCase
{
    /**
     * @var IsCaptchaEnabledInterface|MockObject
     */
    protected $isCaptchaEnabled;

    /**
     * @var CreateAccountButton|MockObject
     */
    protected $subject;

    /**
     * @var DisableCreateAccountButton
     */
    protected $plugin;

    protected function setUp(): void
    {
        $this->isCaptchaEnabled = $this->getMockForAbstractClass(
            IsCaptchaEnabledInterface::class
        );
        $this->subject = $this->createMock(CreateAccountButton::class);

        $this->plugin = new DisableCreateAccountButton(
            $this->isCaptchaEnabled
        );
    }

    public function testAfterEnabled()
    {
        $key = 'customer_create';
        $this->isCaptchaEnabled->expects($this->once())
            ->method('isCaptchaEnabledFor')->with($key)->willReturn(true);
        $this->assertEquals(true, $this->plugin->afterDisabled($this->subject));
    }
}
