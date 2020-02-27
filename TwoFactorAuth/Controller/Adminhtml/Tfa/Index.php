<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Controller\Adminhtml\Tfa;

use Magento\Backend\Model\Auth\Session;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use Magento\TwoFactorAuth\Controller\Adminhtml\AbstractAction;
use Magento\User\Model\User;
use Magento\TwoFactorAuth\Api\UserConfigRequestManagerInterface;

/**
 * 2FA entry point controller
 */
class Index extends AbstractAction implements HttpGetActionInterface
{
    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_TwoFactorAuth::tfa';

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
     * @var Context
     */
    private $context;

    /**
     * @var UserConfigRequestManagerInterface
     */
    private $userConfigRequest;

    /**
     * @param Action\Context $context
     * @param Session $session
     * @param UserConfigManagerInterface $userConfigManager
     * @param TfaInterface $tfa
     * @param UserConfigRequestManagerInterface $userConfigRequestManager
     */
    public function __construct(
        Context $context,
        Session $session,
        UserConfigManagerInterface $userConfigManager,
        TfaInterface $tfa,
        UserConfigRequestManagerInterface $userConfigRequestManager
    ) {
        parent::__construct($context);
        $this->tfa = $tfa;
        $this->session = $session;
        $this->userConfigManager = $userConfigManager;
        $this->context = $context;
        $this->userConfigRequest = $userConfigRequestManager;
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

        if (!$this->tfa->getUserProviders((int)$user->getId())) {
            //If 2FA is not configured - request configuration.
            return $this->_redirect('tfa/tfa/requestconfig');
        }
        $providersToConfigure = $this->tfa->getProvidersToActivate((int) $user->getId());
        if (!empty($providersToConfigure)) {
            //2FA provider not activated - redirect to the provider form.
            return $this->_redirect($providersToConfigure[0]->getConfigureAction());
        }

        $providerCode = '';

        $defaultProviderCode = $this->userConfigManager->getDefaultProvider((int) $user->getId());
        if ($this->tfa->getProviderIsAllowed((int) $user->getId(), $defaultProviderCode)) {
            //If default provider was configured - select it.
            $providerCode = $defaultProviderCode;
        }

        if (!$providerCode) {
            //Select one random provider.
            $providers = $this->tfa->getUserProviders((int) $user->getId());
            if (!empty($providers)) {
                $providerCode = $providers[0]->getCode();
            }
        }

        if (!$providerCode) {
            //Couldn't find provider - perhaps something is not configured properly.
            return $this->_redirect($this->context->getBackendUrl()->getStartupPageUrl());
        }

        $provider = $this->tfa->getProvider($providerCode);
        if ($provider) {
            //Provider found, user will be challenged.
            return $this->_redirect($provider->getAuthAction());
        }

        throw new LocalizedException(__('Internal error accessing 2FA index page'));
    }
}
