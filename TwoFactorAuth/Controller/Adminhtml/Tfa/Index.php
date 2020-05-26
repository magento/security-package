<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Controller\Adminhtml\Tfa;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\TfaSessionInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use Magento\TwoFactorAuth\Controller\Adminhtml\AbstractAction;
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
     * @var TfaSessionInterface
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
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @param Context $context
     * @param TfaSessionInterface $session
     * @param UserConfigManagerInterface $userConfigManager
     * @param TfaInterface $tfa
     * @param UserConfigRequestManagerInterface $userConfigRequestManager
     * @param UserContextInterface $userContext
     */
    public function __construct(
        Context $context,
        TfaSessionInterface $session,
        UserConfigManagerInterface $userConfigManager,
        TfaInterface $tfa,
        UserConfigRequestManagerInterface $userConfigRequestManager,
        UserContextInterface $userContext
    ) {
        parent::__construct($context);
        $this->tfa = $tfa;
        $this->session = $session;
        $this->userConfigManager = $userConfigManager;
        $this->context = $context;
        $this->userConfigRequest = $userConfigRequestManager;
        $this->userContext = $userContext;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $userId = $this->userContext->getUserId();

        if (!$this->tfa->getUserProviders($userId)) {
            //If 2FA is not configured - request configuration.
            return $this->_redirect('tfa/tfa/requestconfig');
        }

        $providersToConfigure = $this->tfa->getProvidersToActivate($userId);
        $toActivateCodes = [];
        foreach ($providersToConfigure as $toActivateProvider) {
            $toActivateCodes[] = $toActivateProvider->getCode();
        }
        $currentlySkipped = array_keys($this->session->getSkippedProviderConfig());
        $notSkippedProvidersToConfigured = array_diff($toActivateCodes, $currentlySkipped);

        if ($notSkippedProvidersToConfigured) {
            foreach ($providersToConfigure as $providerToConfigure) {
                if (in_array($providerToConfigure->getCode(), $notSkippedProvidersToConfigured)) {
                    //2FA provider not activated - redirect to the provider form.
                    return $this->_redirect($providerToConfigure->getConfigureAction());
                }
            }
        }

        $providerCode = '';

        $defaultProviderCode = $this->userConfigManager->getDefaultProvider($userId);
        if ($this->tfa->getProviderIsAllowed($userId, $defaultProviderCode)
            && $this->tfa->getProvider($defaultProviderCode)->isActive($userId)
        ) {
            //If default provider was configured - select it.
            $providerCode = $defaultProviderCode;
        }

        if (!$providerCode) {
            //Select one random provider.
            $providers = $this->tfa->getUserProviders($userId);
            if (!empty($providers)) {
                foreach ($providers as $enabledProvider) {
                    /*
                     * The user has skipped all providers that need to be configured but there is
                     * also at least one already configured
                     */
                    if (!in_array($enabledProvider->getCode(), $currentlySkipped)
                        && !in_array($enabledProvider->getCode(), $toActivateCodes)
                    ) {
                        $providerCode = $enabledProvider->getCode();
                        break;
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
