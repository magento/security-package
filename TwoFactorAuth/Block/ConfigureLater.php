<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Block;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;

/**
 * @api
 */
class ConfigureLater extends Template
{
    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * ChangeProvider constructor.
     * @param Context $context
     * @param TfaInterface $tfa
     * @param SerializerInterface $serializer
     * @param FormKey $formKey
     * @param UserContextInterface $userContext
     * @param array $data
     */
    public function __construct(
        Context $context,
        TfaInterface $tfa,
        SerializerInterface $serializer,
        FormKey $formKey,
        UserContextInterface $userContext,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->tfa = $tfa;
        $this->serializer = $serializer;
        $this->formKey = $formKey;
        $this->userContext = $userContext;
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml()
    {
        $userId = $this->userContext->getUserId();
        $providers = $this->tfa->getUserProviders($userId);
        $toActivate = $this->tfa->getProvidersToActivate($userId);

        foreach ($toActivate as $toActivateProvider) {
            if ($toActivateProvider->getCode() === $this->getData('provider') && count($providers) > 1) {
                return parent::_toHtml();
            }
        }

        return '';
    }

    /**
     * Get a serialized string of post data for the configure later endpoint
     *
     * @return string
     */
    public function getPostData(): string
    {
        return $this->serializer->serialize(
            [
                'action' => $this->getUrl('tfa/tfa/configurelater'),
                'data' => [
                    'provider' => $this->getData('provider'),
                    'form_key' => $this->formKey->getFormKey()
                ]
            ]
        );
    }
}
