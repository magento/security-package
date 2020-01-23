<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Controller\Adminhtml\Duo;

use Magento\Backend\Model\Auth\Session;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\DataObjectFactory;
use Magento\TwoFactorAuth\Model\AlertInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\TfaSessionInterface;
use Magento\TwoFactorAuth\Controller\Adminhtml\AbstractAction;
use Magento\TwoFactorAuth\Model\Provider\Engine\DuoSecurity;

/**
 * Duo security authentication post controller
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Authpost extends AbstractAction implements HttpPostActionInterface
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
     * @var TfaSessionInterface
     */
    private $tfaSession;

    /**
     * @var DuoSecurity
     */
    private $duoSecurity;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var AlertInterface
     */
    private $alert;

    /**
     * @var Action\Context
     */
    private $context;

    /**
     * Authpost constructor.
     * @param Action\Context $context
     * @param Session $session
     * @param DuoSecurity $duoSecurity
     * @param TfaSessionInterface $tfaSession
     * @param DataObjectFactory $dataObjectFactory
     * @param AlertInterface $alert
     * @param TfaInterface $tfa
     */
    public function __construct(
        Action\Context $context,
        Session $session,
        DuoSecurity $duoSecurity,
        TfaSessionInterface $tfaSession,
        DataObjectFactory $dataObjectFactory,
        AlertInterface $alert,
        TfaInterface $tfa
    ) {
        parent::__construct($context);
        $this->tfa = $tfa;
        $this->session = $session;
        $this->tfaSession = $tfaSession;
        $this->duoSecurity = $duoSecurity;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->alert = $alert;
        $this->context = $context;
    }

    /**
     * Get current user
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
        $user = $this->getUser();

        if ($this->duoSecurity->verify($user, $this->dataObjectFactory->create([
            'data' => $this->getRequest()->getParams(),
        ]))) {
            $this->tfa->getProvider(DuoSecurity::CODE)->activate((int) $user->getId());
            $this->tfaSession->grantAccess();
            return $this->_redirect($this->context->getBackendUrl()->getStartupPageUrl());
        } else {
            $this->alert->event(
                'Magento_TwoFactorAuth',
                'DuoSecurity invalid auth',
                AlertInterface::LEVEL_WARNING,
                $user->getUserName()
            );

            return $this->_redirect('*/*/auth');
        }
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        // Do not check for activation
        $user = $this->getUser();

        return
            $user &&
            $this->tfa->getProviderIsAllowed((int) $user->getId(), DuoSecurity::CODE);
    }
}
