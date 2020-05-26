<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Controller\Adminhtml\Duo;

use Magento\Backend\Model\Auth\Session;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use Magento\TwoFactorAuth\Controller\Adminhtml\AbstractAction;
use Magento\TwoFactorAuth\Model\Provider\Engine\DuoSecurity;
use Magento\TwoFactorAuth\Model\UserConfig\HtmlAreaTokenVerifier;

/**
 * Duo security authentication page
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Auth extends AbstractAction implements HttpGetActionInterface
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
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var UserConfigManagerInterface
     */
    private $userConfigManager;

    /**
     * @var HtmlAreaTokenVerifier
     */
    private $tokenVerifier;

    /**
     * @param Action\Context $context
     * @param Session $session
     * @param PageFactory $pageFactory
     * @param UserConfigManagerInterface $userConfigManager
     * @param TfaInterface $tfa
     * @param HtmlAreaTokenVerifier $tokenVerifier
     */
    public function __construct(
        Action\Context $context,
        Session $session,
        PageFactory $pageFactory,
        UserConfigManagerInterface $userConfigManager,
        TfaInterface $tfa,
        HtmlAreaTokenVerifier $tokenVerifier
    ) {
        parent::__construct($context);
        $this->tfa = $tfa;
        $this->session = $session;
        $this->pageFactory = $pageFactory;
        $this->userConfigManager = $userConfigManager;
        $this->tokenVerifier = $tokenVerifier;
    }

    /**
     * Get current user
     *
     * @return \Magento\User\Model\User|null
     */
    private function getUser()
    {
        return $this->session->getUser();
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $this->userConfigManager->setDefaultProvider((int)$this->getUser()->getId(), DuoSecurity::CODE);
        return $this->pageFactory->create();
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        if (!parent::_isAllowed()) {
            return false;
        }

        // 1st time users must have the token.
        $user = $this->getUser();

        return
            $user &&
            $this->tfa->getProviderIsAllowed((int)$user->getId(), DuoSecurity::CODE)
            && (
                $this->userConfigManager->isProviderConfigurationActive((int)$user->getId(), DuoSecurity::CODE)
                || $this->tokenVerifier->isConfigTokenProvided()
            );
    }
}
