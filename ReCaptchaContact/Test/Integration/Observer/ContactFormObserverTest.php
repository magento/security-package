<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaContact\Test\Integration\Observer;

use Magento\Customer\Model\AccountConfirmation;
use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Element\Message\InterpretationStrategyInterface;
use Magento\Framework\Message\Manager as MessageManager;
use Magento\ReCaptcha\Model\CaptchaValidator;
use Magento\ReCaptchaApi\Api\CaptchaValidatorInterface;
use Magento\ReCaptchaFrontendUi\Model\CaptchaConfig;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\TestFramework\TestCase\AbstractController;

/**
 *
 */
class ContactFormObserverTest extends AbstractController
{

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var CaptchaValidatorInterface|MockObject
     */
    private $captchaValidatorMock;


    /**
     * @var MutableScopeConfigInterface
     */
    private $settingsConfiguration;

    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * @var InterpretationStrategyInterface
     */
    private $interpretationStrategy;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->response = $this->_objectManager->get(ResponseInterface::class);
        $this->captchaValidatorMock = $this->createMock(CaptchaValidatorInterface::class);
        $this->settingsConfiguration = $this->_objectManager->get(MutableScopeConfigInterface::class);
        $this->messageManager = $this->_objectManager->get(MessageManager::class);
        $this->interpretationStrategy = $this->_objectManager->get(InterpretationStrategyInterface::class);
        $this->_objectManager->addSharedInstance($this->captchaValidatorMock, CaptchaValidator::class);
    }


    /**
     * Test for Recaptcha is Disabled
     *
     * @magentoConfigFixture recaptcha/frontend/enabled_for_contact 0
     */
    public function testReCaptchaDisabled()
    {
        $this->sendContactPostAction();

        $this->assertSessionMessages(
            $this->contains(
                "Thanks for contacting us with your comments and questions. We&#039;ll respond to you very soon."
            ),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * Test for Recaptcha is Enabled
     * Exist access keys
     *
     * @magentoAdminConfigFixture  recaptcha/frontend/enabled_for_contact  1
     */
    public function testCorrectRecaptcha()
    {
        $this->settingsConfiguration->setValue('recaptcha/frontend/public_key', 'test_public_key', ScopeInterface::SCOPE_WEBSITES);
        $this->settingsConfiguration->setValue('recaptcha/frontend/private_key', 'test_private_key', ScopeInterface::SCOPE_WEBSITES);

        $this->captchaValidatorMock->expects($this->once())->method('validate')->willReturn(true);
        $this->sendContactPostAction();

        $this->assertSessionMessages(
            $this->contains(
                "Thanks for contacting us with your comments and questions. We&#039;ll respond to you very soon."
            ),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * Test for Recaptcha is Enabled
     * Exist access keys
     * Test for Incorrect captcha
     *
     * @magentoAdminConfigFixture recaptcha/frontend/enabled_for_contact  1
     */
    public function testIncorrectRecaptcha()
    {
        $this->settingsConfiguration->setValue('recaptcha/frontend/public_key', 'test_public_key', ScopeInterface::SCOPE_WEBSITES);
        $this->settingsConfiguration->setValue('recaptcha/frontend/private_key', 'test_private_key', ScopeInterface::SCOPE_WEBSITES);

        $this->captchaValidatorMock->expects($this->once())->method('validate')->willReturn(false);
        $this->sendContactPostAction();

        /** @var $messages \Magento\Framework\Message\AbstractMessage[] */
        $messages = $this->messageManager->getMessages()->getItems();

        $actualMessages = [];
        foreach ($messages as $message) {
            $actualMessages[] = $this->interpretationStrategy->interpret($message);
        }

        $expected = ['Thanks for contacting us with your comments and questions. We&#039;ll respond to you very soon.'];
        $this->assertNotEquals($expected, $actualMessages);
    }

    /**
     * Send Contact form
     */
    protected function sendContactPostAction()
    {
        $params = [
            'name' => 'customer name',
            'comment' => 'comment',
            'email' => 'user@example.com',
            'hideit' => '',
            'form_key' => $this->formKey->getFormKey(),
            'g-recaptcha-response' => 'test_response'
        ];

        $this->getRequest()->setPostValue($params)->setMethod(HttpRequest::METHOD_POST);
        $this->dispatch('contact/index/post');

        $this->assertRedirect($this->stringContains('contact/index'));
    }

}
