<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Controller\Adminhtml\U2f;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\TwoFactorAuth\Controller\Adminhtml\AbstractAction;
use Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey;
use Magento\TwoFactorAuth\Model\Tfa;
use Magento\User\Model\User;

/**
 * CUbiKey configuration page controller
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Configure extends AbstractAction implements HttpGetActionInterface
{
    /**
     * @var Tfa
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
     * @param Tfa $tfa
     * @param Session $session
     * @param PageFactory $pageFactory
     * @param Action\Context $context
     */
    public function __construct(
        Tfa $tfa,
        Session $session,
        PageFactory $pageFactory,
        Action\Context $context
    ) {

        $this->tfa = $tfa;
        $this->session = $session;
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        return $this->pageFactory->create();
    }

    /**
     * @return User|null
     */
    private function getUser(): ?User
    {
        return $this->session->getUser();
    }

    /**
     * C@inheritDoc
     */
    protected function _isAllowed()
    {
        $user = $this->getUser();

        return
            $user &&
            $this->tfa->getProviderIsAllowed((int) $user->getId(), U2fKey::CODE) &&
            !$this->tfa->getProvider(U2fKey::CODE)->isActive((int) $user->getId());
    }
}
