<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Controller\Adminhtml\Tfa;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Controller\Adminhtml\AbstractAction;
use Magento\Backend\Model\Auth\Session;

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
     * @var Session
     */
    private $session;

    /**
     * @param Context $context
     * @param TfaInterface $tfa
     * @param Session $session
     */
    public function __construct(
        Context $context,
        TfaInterface $tfa,
        Session $session
    ) {
        parent::__construct($context);
        $this->tfa = $tfa;
        $this->session = $session;
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        $userId = (int)$this->session->getUser()->getId();
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
        $userId = (int)$this->session->getUser()->getId();
        $providers = $this->tfa->getUserProviders($userId);
        $needActivation = $this->tfa->getProvidersToActivate($userId);
        $providerCodes = [];
        foreach ($providers as $forcedProvider) {
            $providerCodes[] = $forcedProvider->getCode();
        }

        $currentlySkipped = $this->session->getData('tfa_skipped_config') ?? [];
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

        $this->session->setTfaSkippedConfig($currentlySkipped);

        $redirect = $this->resultRedirectFactory->create();
        return $redirect->setPath('tfa/tfa/index');
    }
}
