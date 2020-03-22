<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Model\AjaxLogin;

use Magento\Framework\App\PlainTextRequestInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\ReCaptchaUi\Model\CaptchaResponseResolverInterface;

/**
 * @inheritdoc
 */
class CaptchaResponseResolver implements CaptchaResponseResolverInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     *
     * @param RequestInterface|PlainTextRequestInterface $request
     * @return string
     * @throws InputException
     */
    public function resolve(RequestInterface $request): string
    {
        $content = $request->getContent();
        if (empty($content)) {
            throw new InputException(__('Can not resolve reCAPTCHA response.'));
        }

        try {
            $jsonParams = $this->serializer->unserialize($content);
        } catch (\InvalidArgumentException $e) {
            throw new InputException(__('Can not resolve reCAPTCHA response.'), $e);
        }

        if (empty($jsonParams[self::PARAM_RECAPTCHA])) {
            throw new InputException(__('Can not resolve reCAPTCHA response.'));
        }
        return $jsonParams[self::PARAM_RECAPTCHA];
    }
}
