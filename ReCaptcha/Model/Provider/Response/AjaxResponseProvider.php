<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model\Provider\Response;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\ReCaptcha\Model\Provider\ResponseProviderInterface;
use Magento\ReCaptcha\Model\ValidateInterface;

/**
 * @inheritDoc
 */
class AjaxResponseProvider implements ResponseProviderInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * AjaxResponseProvider constructor.
     * @param RequestInterface $request
     * @param SerializerInterface $serializer
     */
    public function __construct(
        RequestInterface $request,
        SerializerInterface $serializer
    ) {
        $this->request = $request;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function execute(): string
    {
        if ($content = $this->request->getContent()) {
            try {
                $jsonParams = $this->serializer->decode($content);
                if (isset($jsonParams[ValidateInterface::PARAM_RECAPTCHA_RESPONSE])) {
                    return $jsonParams[ValidateInterface::PARAM_RECAPTCHA_RESPONSE];
                }
            } catch (\Exception $e) {
                return '';
            }
        }

        return '';
    }
}
