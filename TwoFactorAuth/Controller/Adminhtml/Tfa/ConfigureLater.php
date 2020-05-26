<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Controller\Adminhtml\Tfa;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\TfaSessionInterface;
use Magento\TwoFactorAuth\Controller\Adminhtml\AbstractAction;

/**
 * Configure 2FA for the application.
 */
class ConfigureLater extends AbstractAction implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Magento_TwoFactorAuth::tfa';

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var TfaSessionInterface
     */
    private $session;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @param Context $context
     * @param TfaInterface $tfa
     * @param TfaSessionInterface $session
     * @param UserContextInterface $userContext
     */
    public function __construct(
        Context $context,
        TfaInterface $tfa,
        TfaSessionInterface $session,
        UserContextInterface $userContext
    ) {
        parent::__construct($context);
        $this->tfa = $tfa;
        $this->session = $session;
        $this->userContext = $userContext;
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        $userId = $this->userContext->getUserId();
        $providers = $this->tfa->getUserProviders($userId);
        $toActivate = $this->tfa->getProvidersToActivate($userId);

        foreach ($toActivate as $toActivateProvider) {
            if ($toActivateProvider->getCode() === $this->_request->getParam('provider') && count($providers) > 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $provider = $this->getRequest()->getParam('provider');
        $userId = $this->userContext->getUserId();
        $providers = $this->tfa->getUserProviders($userId);
        $needActivation = $this->tfa->getProvidersToActivate($userId);
        $providerCodes = [];
        foreach ($providers as $forcedProvider) {
            $providerCodes[] = $forcedProvider->getCode();
        }

        $currentlySkipped = $this->session->getSkippedProviderConfig();
        $currentlySkipped[$provider] = true;

        // Catch users trying to skip all available providers when there are none configured
        if (count($needActivation) === count($providers)
            && count($providerCodes) === count(array_intersect($providerCodes, array_keys($currentlySkipped)))
        ) {
            $this->messageManager->addErrorMessage(
                __('At least one two-factor authentication provider must be configured.')
            );
            $currentlySkipped = [];
        }

        $this->session->setSkippedProviderConfig($currentlySkipped);

        $redirect = $this->resultRedirectFactory->create();
        return $redirect->setPath('tfa/tfa/index');
    }
}
