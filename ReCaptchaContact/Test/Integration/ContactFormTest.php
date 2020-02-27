<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaContact\Test\Integration;

use Magento\Framework\Exception\InputException;
use Magento\TestFramework\App\ReinitableConfig;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\MessageInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\ReCaptcha\Model\CaptchaValidator;
use Magento\ReCaptchaApi\Api\CaptchaValidatorInterface;
use Magento\ReCaptchaUi\Model\CaptchaResponseResolverInterface;
use Magento\TestFramework\TestCase\AbstractController;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Test for \Magento\ReCaptchaContact\Observer\ContactFormObserver class.
 */
class ContactFormObserverTest extends AbstractController
{
    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var CaptchaValidatorInterface|MockObject
     */
    private $captchaValidatorMock;

    /**
     * @var ReinitableConfig
     */
    private $settingsConfiguration;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->settingsConfiguration = $this->_objectManager->get(ReinitableConfig::class);

        $this->captchaValidatorMock = $this->createMock(CaptchaValidatorInterface::class);
        $this->_objectManager->addSharedInstance($this->captchaValidatorMock, CaptchaValidator::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetRequestIfReCaptchaIsDisabled()
    {
        $this->settingsRecaptcha(false, false);
        $this->checkSuccessfulGetResponse();
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetRequestIfReCaptchaKeysAreNotConfigured()
    {
        $this->settingsRecaptcha(true, false);
        $this->checkSuccessfulGetResponse();
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetRequestIfReCaptchaIsEnabled()
    {
        $this->settingsRecaptcha(true, true);
        $this->checkSuccessfulGetResponse(true);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testPostRequestIfReCaptchaIsDisabled()
    {
        $this->settingsRecaptcha(false, false);
        $this->checkSuccessfulPostResponse();

        $this->assertSessionMessages(
            $this->contains(
                "Thanks for contacting us with your comments and questions. We&#039;ll respond to you very soon."
            ),
            MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testPostRequestIfReCaptchaKeysAreNotConfigured()
    {
        $this->settingsRecaptcha(true, false);
        $this->checkSuccessfulPostResponse();

        $this->assertSessionMessages(
            $this->contains(
                "Thanks for contacting us with your comments and questions. We&#039;ll respond to you very soon."
            ),
            MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testPostRequestWithSuccessfulReCaptchaValidation()
    {
        $this->settingsRecaptcha(true, true);

        $this->captchaValidatorMock->expects($this->once())->method('isValid')->willReturn(true);

        $this->checkSuccessfulPostResponse(
            [
                CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test',
            ]
        );

        $this->assertSessionMessages(
            $this->contains(
                "Thanks for contacting us with your comments and questions. We&#039;ll respond to you very soon."
            ),
            MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testPostRequestIfReCaptchaParameterIsMissed()
    {
        $this->settingsRecaptcha(true, true);

        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Can not resolve reCAPTCHA parameter.');

        $this->getRequest()->setPostValue(
            [
                'form_key' => $this->formKey->getFormKey(),
                'name' => 'customer name',
                'comment' => 'comment',
                'email' => 'user@example.com',
            ]
        )->setMethod(HttpRequest::METHOD_POST);

        $this->dispatch('contact/index/post');

        $this->assertRedirect($this->stringContains('contact/index'));

        $this->assertSessionMessages(
            self::equalTo(['Can not resolve reCAPTCHA parameter.']),
            MessageInterface::TYPE_ERROR
        );
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testPostRequestWithFailedReCaptchaValidation()
    {
        $this->settingsRecaptcha(true, true);

        $this->captchaValidatorMock->expects($this->once())->method('isValid')->willReturn(false);

        $this->getRequest()->setPostValue(
            [
                'form_key' => $this->formKey->getFormKey(),
                'name' => 'customer name',
                'comment' => 'comment',
                'email' => 'user@example.com',
                CaptchaResponseResolverInterface::PARAM_RECAPTCHA => 'test',
            ]

        )->setMethod(HttpRequest::METHOD_POST);

        $this->dispatch('contact/index/post');

        $this->assertRedirect($this->stringContains('contact/index'));

        $this->assertSessionMessages(
            $this->equalTo(
                ['You cannot proceed with such operation, your reCAPTCHA reputation is too low.']
            ),
            MessageInterface::TYPE_ERROR
        );
    }

    /**
     * @param bool $shouldContainReCaptcha
     */
    private function checkSuccessfulGetResponse($shouldContainReCaptcha = false)
    {
        $this->dispatch('contact/index');
        $content = $this->getResponse()->getBody();

        self::assertNotEmpty($content);

        $shouldContainReCaptcha
            ? $this->assertContains('recaptcha', $content)
            : $this->assertNotContains('recaptcha', $content);

        self::assertEmpty($this->getSessionMessages(MessageInterface::TYPE_ERROR));
    }

    /**
     * @param array $postValues
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function checkSuccessfulPostResponse(array $postValues = [])
    {
        $this->getRequest()->setPostValue(array_replace_recursive(
            [
                'form_key' => $this->formKey->getFormKey(),
                'name' => 'customer name',
                'comment' => 'comment',
                'email' => 'user@example.com',
            ],
            $postValues
        ))->setMethod(HttpRequest::METHOD_POST);

        $this->dispatch('contact/index/post');

        $this->assertRedirect($this->stringContains('contact/index'));
    }

    /**
     * @param bool $captchaIsEnabledForContact
     * @param bool $captchaIsConfigured
     */
    private function settingsRecaptcha($captchaIsEnabledForContact = false, $captchaIsConfigured = false)
    {
        if ($captchaIsEnabledForContact) {
            $this->settingsConfiguration->setValue(
                'recaptcha/frontend/enabled_for_contact',
                (int)$captchaIsEnabledForContact,
                ScopeInterface::SCOPE_WEBSITES
            );
        }


        if ($captchaIsConfigured) {
            $this->settingsConfiguration->setValue(
                'recaptcha/frontend/public_key',
                'test_public_key',
                ScopeInterface::SCOPE_WEBSITES
            );
            $this->settingsConfiguration->setValue(
                'recaptcha/frontend/private_key',
                'test_private_key',
                ScopeInterface::SCOPE_WEBSITES
            );
        }
    }

}
