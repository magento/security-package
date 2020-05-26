<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Block;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Backend\Block\Template;
use Magento\Backend\Model\Auth\Session;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\ProviderInterface;

/**
 * Represent the change providers block for authentication workflow
 *
 * @api
 */
class ChangeProvider extends Template
{
    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param Template\Context $context
     * @param Session $session
     * @param UserContextInterface $userContext
     * @param TfaInterface $tfa
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Session $session,
        UserContextInterface $userContext,
        TfaInterface $tfa,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->tfa = $tfa;
        $this->session = $session;
        $this->userContext = $userContext;
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml()
    {
        $toActivate = $this->tfa->getProvidersToActivate($this->userContext->getUserId());

        foreach ($toActivate as $toActivateProvider) {
            if ($toActivateProvider->getCode() === $this->getData('provider')) {
                return '';
            }
        }

        return parent::_toHtml();
    }

    /**
     * @inheritdoc
     */
    public function getJsLayout()
    {
        $providers = [];
        foreach ($this->getProvidersList() as $provider) {
            if (!$provider->isActive($this->userContext->getUserId())) {
                continue;
            }
            $providers[] = [
                'code' => $provider->getCode(),
                'name' => $provider->getName(),
                'auth' => $this->getUrl($provider->getAuthAction()),
                'icon' => $this->getViewFileUrl($provider->getIcon()),
            ];
        }

        $this->jsLayout['components']['tfa-change-provider']['switchIcon'] =
            $this->getViewFileUrl('Magento_TwoFactorAuth::images/change_provider.png');
        $this->jsLayout['components']['tfa-change-provider']['providers'] = $providers;

        return parent::getJsLayout();
    }

    /**
     * Get a list of available providers
     *
     * @return ProviderInterface[]
     */
    private function getProvidersList(): array
    {
        $res = [];

        $providers = $this->tfa->getUserProviders((int) $this->userContext->getUserId());
        foreach ($providers as $provider) {
            if ($provider->getCode() !== $this->getData('provider')) {
                $res[] = $provider;
            }
        }

        return $res;
    }
}
