<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaSendFriend\Test\Api\GraphQl\SendFriend;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\GraphQlAbstract;

/**
 * GraphQl test for send email to friend functionality with ReCaptcha enabled.
 */
class SendEmailToFriendTest extends GraphQlAbstract
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->productRepository = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);
    }

    /**
     * @magentoApiDataFixture Magento/GraphQl/Catalog/_files/simple_product.php
     * @magentoConfigFixture default_store sendfriend/email/enabled 1
     * @magentoConfigFixture default_store sendfriend/email/allow_guest 1
     * @magentoConfigFixture default_store sendfriend/email/allow_guest 1
     * @magentoConfigFixture default_store customer/captcha/enable 0
     *
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/public_key test_public_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_invisible/private_key test_private_key
     * @magentoConfigFixture base_website recaptcha_frontend/type_for/sendfriend invisible
     *
     * @return void
     */
    public function testSendFriendReCaptchaValidationFailed(): void
    {
        $productId = (int)$this->productRepository->get('simple_product')->getId();
        $recipients = '{
                  name: "Recipient Name 1"
                  email:"recipient1@mail.com"
               },
              {
                  name: "Recipient Name 2"
                  email:"recipient2@mail.com"
              }';
        $query = $this->getQuery($productId, $recipients);

        $this->expectExceptionMessage('ReCaptcha validation failed, please try again');
        $this->graphQlMutation($query);
    }

    /**
     * @param int $productId
     * @param string $recipients
     * @return string
     */
    private function getQuery(int $productId, string $recipients): string
    {
        return <<<QUERY
mutation {
    sendEmailToFriend(
        input: {
          product_id: {$productId}
          sender: {
            name: "Name"
            email: "e@mail.com"
            message: "Lorem Ipsum"
        }
          recipients: [{$recipients}]
        }
    ) {
        sender {
            name
            email
            message
        }
        recipients {
            name
            email
        }
    }
}
QUERY;
    }
}
