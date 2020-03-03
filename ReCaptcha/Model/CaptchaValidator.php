<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

use Magento\Framework\Validation\ValidationException;
use Magento\Framework\Validation\ValidationResultFactory;
use Magento\ReCaptchaApi\Api\CaptchaValidatorInterface;
use Magento\ReCaptchaApi\Api\Data\ValidationConfigInterface;
use ReCaptcha\ReCaptcha;
use Magento\ReCaptchaApi\Model\ErrorLabels;

/**
 * @inheritDoc
 */
class CaptchaValidator implements CaptchaValidatorInterface
{
    /**
     * @var ErrorLabels
     */
    private $errorLabels;

    /**
     * @var ValidationResultFactory
     */
    private $validationResultFactory;

    /**
     * @param ValidationResultFactory $validationResultFactory
     * @param ErrorLabels $errorLabels
     */
    public function __construct(
        ValidationResultFactory $validationResultFactory,
        ErrorLabels $errorLabels
    ) {
        $this->validationResultFactory = $validationResultFactory;
        $this->errorLabels = $errorLabels;
    }

    /**
     * @inheritdoc
     */
    public function isValid(
        string $reCaptchaResponse,
        ValidationConfigInterface $validationConfig
    ): bool {
        $secret = $validationConfig->getPrivateKey();

        if ($reCaptchaResponse) {
            // @codingStandardsIgnoreStart
            $reCaptcha = new ReCaptcha($secret);
            // @codingStandardsIgnoreEmd

            if ($validationConfig->getCaptchaType() === 'recaptcha_v3') {
                if (isset($options['threshold'])) {
                    $reCaptcha->setScoreThreshold($validationConfig->getScoreThreshold());
                }
            }

            $res = $reCaptcha->verify($reCaptchaResponse, $validationConfig->getRemoteIp());

            if ($res->getErrorCodes() && is_array($res->getErrorCodes()) && count($res->getErrorCodes()) > 0) {
                $validationErrors = [];

                foreach ($res->getErrorCodes() as $errorCode) {
                    $validationErrors[] = __($this->errorLabels->getErrorCodeLabel($errorCode));
                }

                $validationResult = $this->validationResultFactory->create(['errors' => $validationErrors]);

                if (false === $validationResult->isValid()) {
                    throw new ValidationException(__('Validation Failed.'), null, 0, $validationResult);
                }
            }
        }
    }

}
