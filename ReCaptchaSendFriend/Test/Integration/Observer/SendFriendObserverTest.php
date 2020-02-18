<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SendFriendObserverTest\Test\Integration\Observer;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\App\ReinitableConfig;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Message\InterpretationStrategyInterface;
use Magento\Framework\Message\Manager as MessageManager;
use Magento\ReCaptcha\Model\CaptchaValidator;
use Magento\ReCaptchaApi\Api\CaptchaValidatorInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\Request;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\TestFramework\TestCase\AbstractController;
use Magento\TestFramework\Bootstrap;

/**
 * Test for \Magento\ReCaptchaSendFriend\Observer\SendFriendObserverTest class.
 */
class SendFriendObserverTest extends AbstractController
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
     * @var ReinitableConfig
     */
    private $settingsConfiguration;

    /**
     * @var InterpretationStrategyInterface
     */
    private $interpretationStrategy;

    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->response = $this->_objectManager->get(ResponseInterface::class);
        $this->captchaValidatorMock = $this->createMock(CaptchaValidatorInterface::class);
        $this->settingsConfiguration = $this->_objectManager->get(ReinitableConfig::class);
        $this->interpretationStrategy = $this->_objectManager->get(InterpretationStrategyInterface::class);
        $this->messageManager = $this->_objectManager->get(MessageManager::class);
        $this->_objectManager->addSharedInstance($this->captchaValidatorMock, CaptchaValidator::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default_store sendfriend/email/enabled 1
     * @magentoConfigFixture default_store sendfriend/email/allow_guest 1
     * @magentoDataFixture Magento/Catalog/_files/products.php
     */
    public function testRecaptchaNotConfigured()
    {
        $product = $this->getProduct();
        $this->prepareRequestData(false, false);

        $this->dispatch('sendfriend/product/sendmail/id/' . $product->getId());
        $this->assertSessionMessages(
            $this->equalTo(['The link to a friend was sent.']),
            MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default_store sendfriend/email/enabled 1
     * @magentoConfigFixture default_store sendfriend/email/allow_guest 1
     * @magentoDataFixture Magento/Catalog/_files/products.php
     */
    public function testReCaptchaDisabled()
    {
        $product = $this->getProduct();
        $this->prepareRequestData(true, false);

        $this->dispatch('sendfriend/product/sendmail/id/' . $product->getId());
        $this->assertSessionMessages(
            $this->equalTo(['The link to a friend was sent.']),
            MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default_store sendfriend/email/enabled 1
     * @magentoConfigFixture default_store sendfriend/email/allow_guest 1
     * @magentoDataFixture Magento/Catalog/_files/products.php
     */
    public function testCorrectRecaptcha()
    {
        $this->captchaValidatorMock->expects($this->once())->method('validate')->willReturn(true);

        $product = $this->getProduct();
        $this->prepareRequestData(true, true);

        $this->dispatch('sendfriend/product/sendmail/id/' . $product->getId());
        $this->assertSessionMessages(
            $this->equalTo(['The link to a friend was sent.']),
            MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default_store sendfriend/email/enabled 1
     * @magentoConfigFixture default_store sendfriend/email/allow_guest 1
     * @magentoDataFixture Magento/Catalog/_files/products.php
     */
    public function testIncorrectRecaptcha()
    {
        $this->captchaValidatorMock->expects($this->once())->method('validate')->willReturn(false);

        $product = $this->getProduct();
        $this->prepareRequestData(true, true);

        $this->dispatch('sendfriend/product/sendmail/id/' . $product->getId());

        /** @var $messages \Magento\Framework\Message\AbstractMessage[] */
        $messages = $this->messageManager->getMessages()->getItems();

        $actualMessages = [];
        foreach ($messages as $message) {
            $actualMessages[] = $this->interpretationStrategy->interpret($message);
        }

        $expected = ['The link to a friend was sent.'];
        $this->assertNotEquals($expected, $actualMessages);
    }

    /**
     * @return ProductInterface
     */
    private function getProduct()
    {
        return $this->_objectManager->get(ProductRepositoryInterface::class)->get('custom-design-simple-product');
    }

    /**
     * @param bool $captchaIsEnabled
     * @param bool $captchaIsEnabledForSendfriend
     * @throws LocalizedException
     */
    private function prepareRequestData(bool $captchaIsEnabled = true, bool $captchaIsEnabledForSendfriend = true)
    {
        if ($captchaIsEnabled) {
            $this->settingsConfiguration->setValue(
                'recaptcha/frontend/enabled_for_sendfriend',
                (int)$captchaIsEnabledForSendfriend,
                ScopeInterface::SCOPE_WEBSITES
            );
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

        /** @var FormKey $formKey */
        $formKey = $this->_objectManager->get(FormKey::class);
        $post = [
            'sender' => [
                'name' => 'Test',
                'email' => 'test@example.com',
                'message' => 'Message',
            ],
            'recipients' => [
                'name' => [
                    'Recipient 1',
                    'Recipient 2'
                ],
                'email' => [
                    'r1@example.com',
                    'r2@example.com'
                ]
            ],
            'form_key' => $formKey->getFormKey(),
        ];

        if ($captchaIsEnabled && $captchaIsEnabledForSendfriend) {
            $post['g-recaptcha-response'] = 'test_response';
        }

        $this->getRequest()->setMethod(Request::METHOD_POST);
        $this->getRequest()->setPostValue($post);
    }

}
