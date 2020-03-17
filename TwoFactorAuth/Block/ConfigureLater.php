<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Block;

use Magento\Backend\Block\Template;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\User\Model\User;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\ProviderInterface;

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
     * @var Session
     */
    private $session;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * ChangeProvider constructor.
     * @param Template\Context $context
     * @param Session $session
     * @param TfaInterface $tfa
     * @param SerializerInterface $serializer
     * @param FormKey $formKey
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Session $session,
        TfaInterface $tfa,
        SerializerInterface $serializer,
        FormKey $formKey,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->tfa = $tfa;
        $this->session = $session;
        $this->serializer = $serializer;
        $this->formKey = $formKey;
    }

    protected function _toHtml()
    {
        $userId = (int)$this->session->getUser()->getId();
        $forced = $this->tfa->getUserProviders($userId);
        $toActivate = $this->tfa->getProvidersToActivate($userId);

        if (count($toActivate) === count($forced)) {
            return '';
        }

        return parent::_toHtml();
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
