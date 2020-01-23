<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Controller\Adminhtml\Tfa;

use Magento\Backend\Model\Auth\Session;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use Magento\TwoFactorAuth\Controller\Adminhtml\AbstractAction;
use Magento\User\Model\User;

/**
 * 2FA entry point controller
 */
class Index extends AbstractAction implements HttpGetActionInterface
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
     * @var UserConfigManagerInterface
     */
    private $userConfigManager;

    /**
     * @var Action\Context
     */
    private $context;

    /**
     * @param Action\Context $context
     * @param Session $session
     * @param UserConfigManagerInterface $userConfigManager
     * @param TfaInterface $tfa
     */
    public function __construct(
        Action\Context $context,
        Session $session,
        UserConfigManagerInterface $userConfigManager,
        TfaInterface $tfa
    ) {
        parent::__construct($context);
        $this->tfa = $tfa;
        $this->session = $session;
        $this->userConfigManager = $userConfigManager;
        $this->context = $context;
    }

    /**
     * Get current user
     * @return User|null
     */
    private function getUser(): ?User
    {
        return $this->session->getUser();
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function execute()
    {
        $user = $this->getUser();

        $providersToConfigure = $this->tfa->getProvidersToActivate((int) $user->getId());
        if (!empty($providersToConfigure)) {
            return $this->_redirect($providersToConfigure[0]->getConfigureAction());
        }

        $providerCode = '';

        $defaultProviderCode = $this->userConfigManager->getDefaultProvider((int) $user->getId());
        if ($this->tfa->getProviderIsAllowed((int) $user->getId(), $defaultProviderCode)) {
            $providerCode = $defaultProviderCode;
        }

        if (!$providerCode) {
            $providers = $this->tfa->getUserProviders((int) $user->getId());
            if (!empty($providers)) {
                $providerCode = $providers[0]->getCode();
            }
        }

        if (!$providerCode) {
            return $this->_redirect($this->context->getBackendUrl()->getStartupPageUrl());
        }

        $provider = $this->tfa->getProvider($providerCode);
        if ($provider) {
            return $this->_redirect($provider->getAuthAction());
        }

        throw new LocalizedException(__('Internal error accessing 2FA index page'));
    }
}
