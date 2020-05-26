<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Controller\Adminhtml\Authy;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use Magento\TwoFactorAuth\Controller\Adminhtml\AbstractAction;
use Magento\TwoFactorAuth\Model\Provider\Engine\Authy;
use Magento\User\Model\User;

/**
 * Verify authy code
 *
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Verify extends AbstractAction implements HttpPostActionInterface, HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var UserConfigManagerInterface
     */
    private $userConfigManager;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * Verify constructor.
     * @param Action\Context $context
     * @param Session $session
     * @param TfaInterface $tfa
     * @param Registry $registry
     * @param UserConfigManagerInterface $userConfigManager
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Action\Context $context,
        Session $session,
        TfaInterface $tfa,
        Registry $registry,
        UserConfigManagerInterface $userConfigManager,
        PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
        $this->session = $session;
        $this->tfa = $tfa;
        $this->userConfigManager = $userConfigManager;
        $this->registry = $registry;
    }

    /**
     * Get current user
     *
     * @return User|null
     */
    private function getUser(): ?User
    {
        return $this->session->getUser();
    }

    /**
     * Get verify information
     *
     * @return verify payload
     * @throws NoSuchEntityException
     */
    private function getVerifyInformation()
    {
        $providerConfig = $this->userConfigManager->getProviderConfig((int) $this->getUser()->getId(), Authy::CODE);
        if (!isset($providerConfig['verify'])) {
            return null;
        }

        return $providerConfig['verify'];
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $verifyInfo = $this->getVerifyInformation();
        $this->registry->register('tfa_authy_verify', $verifyInfo);

        return $this->pageFactory->create();
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        $user = $this->getUser();

        return
            $user &&
            $this->tfa->getProviderIsAllowed((int) $user->getId(), Authy::CODE) &&
            $this->getVerifyInformation() &&
            !$this->tfa->getProvider(Authy::CODE)->isActive((int) $user->getId());
    }
}
