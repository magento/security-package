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
    // To give the email link a place to set the token without causing a loop
    protected $_publicActions = ['index'];

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
        $toActivateCodes = [];
        foreach ($providersToConfigure as $toActivateProvider) {
            $toActivateCodes[] = $toActivateProvider->getCode();
        }
        $currentlySkipped = array_keys($this->session->getData('tfa_skipped_config') ?? []);
        $configRemaining = array_diff($toActivateCodes, $currentlySkipped);

        if (!empty($providersToConfigure) && $configRemaining) {
            foreach ($providersToConfigure as $providerToConfigure) {
                if (in_array($providerToConfigure->getCode(), $configRemaining)) {
                    //2FA provider not activated - redirect to the provider form.
                    return $this->_redirect($providerToConfigure->getConfigureAction());
                }
            }
        }

        $providerCode = '';

        $defaultProviderCode = $this->userConfigManager->getDefaultProvider((int) $user->getId());
        if ($this->tfa->getProviderIsAllowed((int) $user->getId(), $defaultProviderCode)
            && !in_array($defaultProviderCode, $currentlySkipped)
        ) {
            //If default provider was configured - select it.
            $providerCode = $defaultProviderCode;
        }

        if (!$providerCode) {
            //Select one random provider.
            $providers = $this->tfa->getUserProviders((int) $user->getId());
            if (!empty($providers)) {
                foreach ($providers as $enabledProvider) {
                    if (!in_array($enabledProvider->getCode(), $currentlySkipped)) {
                        $providerCode = $enabledProvider->getCode();
                        continue;
                    }
                }
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
