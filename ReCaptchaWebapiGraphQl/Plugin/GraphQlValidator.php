<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaWebapiGraphQl\Plugin;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\GraphQl\Config\ConfigElementInterface;
use Magento\Framework\GraphQl\Config\Element\TypeInterface;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Schema\Type\Output\ElementMapper\Formatter\Fields;
use Magento\ReCaptchaValidationApi\Api\ValidatorInterface;
use Magento\ReCaptchaWebapiApi\Api\WebapiValidationConfigProviderInterface;
use Magento\ReCaptchaWebapiApi\Model\Data\EndpointFactory;

/**
 * Validate ReCaptcha for GraphQl mutations.
 */
class GraphQlValidator
{
    /**
     * @var HttpRequest
     */
    private $request;

    /**
     * @var WebapiValidationConfigProviderInterface
     */
    private $configProvider;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var EndpointFactory
     */
    private $endpointFactory;

    /**
     * @param HttpRequest $request
     * @param WebapiValidationConfigProviderInterface $configProvider
     * @param ValidatorInterface $validator
     * @param EndpointFactory $endpointFactory
     */
    public function __construct(
        HttpRequest $request,
        WebapiValidationConfigProviderInterface $configProvider,
        ValidatorInterface $validator,
        EndpointFactory $endpointFactory
    ) {
        $this->request = $request;
        $this->configProvider = $configProvider;
        $this->validator = $validator;
        $this->endpointFactory = $endpointFactory;
    }

    /**
     * Validate ReCaptcha for mutations if needed.
     *
     * @param Fields $subject
     * @param ConfigElementInterface $configElement
     * @throws GraphQlInputException
     * @return void
     */
    public function beforeFormat(
        Fields $subject,
        ConfigElementInterface $configElement
    ): void {
        if ($configElement->getName() === 'Mutation' && $configElement instanceof TypeInterface) {
            foreach ($configElement->getFields() as $field) {
                $reCaptchaConfig = $this->configProvider->getConfigFor(
                    $this->endpointFactory->create([
                        'class' => ltrim($field->getResolver(), '\\'),
                        'method' => 'resolve',
                        'name' => $field->getName()
                    ])
                );
                if ($reCaptchaConfig
                    && !$this->validator->isValid(
                        (string)$this->request->getHeader('X-ReCaptcha'),
                        $reCaptchaConfig
                    )->isValid()
                ) {
                    throw new GraphQlInputException(__('ReCaptcha validation failed, please try again'));
                }
            }
        }
    }
}
