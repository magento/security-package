<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Controller\Adminhtml\U2f;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\TwoFactorAuth\Model\AlertInterface;
use Magento\TwoFactorAuth\Api\TfaSessionInterface;
use Magento\TwoFactorAuth\Controller\Adminhtml\AbstractAction;
use Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey;
use Magento\TwoFactorAuth\Model\Tfa;
use Magento\User\Model\User;

/**
 * UbiKey configuration post controller
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Configurepost extends AbstractAction implements HttpPostActionInterface
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
     * @var U2fKey
     */
    private $u2fKey;

    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var TfaSessionInterface
     */
    private $tfaSession;

    /**
     * @var AlertInterface
     */
    private $alert;

    /**
     * @param Tfa $tfa
     * @param Session $session
     * @param JsonFactory $jsonFactory
     * @param TfaSessionInterface $tfaSession
     * @param U2fKey $u2fKey
     * @param AlertInterface $alert
     * @param Action\Context $context
     */
    public function __construct(
        Tfa $tfa,
        Session $session,
        JsonFactory $jsonFactory,
        TfaSessionInterface $tfaSession,
        U2fKey $u2fKey,
        AlertInterface $alert,
        Action\Context $context
    ) {
        parent::__construct($context);

        $this->tfa = $tfa;
        $this->session = $session;
        $this->u2fKey = $u2fKey;
        $this->jsonFactory = $jsonFactory;
        $this->tfaSession = $tfaSession;
        $this->alert = $alert;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = $this->jsonFactory->create();

        try {
            $request = $this->getRequest()->getParam('request');
            $response = $this->getRequest()->getParam('response');

            $this->u2fKey->registerDevice($this->getUser(), $request, $response);
            $this->tfaSession->grantAccess();

            $this->alert->event(
                'Magento_TwoFactorAuth',
                'U2F New device registered',
                AlertInterface::LEVEL_INFO,
                $this->getUser()->getUserName()
            );

            $res = ['success' => true];
        } catch (Exception $e) {
            $this->alert->event(
                'Magento_TwoFactorAuth',
                'U2F error while adding device',
                AlertInterface::LEVEL_ERROR,
                $this->getUser()->getUserName(),
                $e->getMessage()
            );

            $res = ['success' => false, 'message' => $e->getMessage()];
        }

        $result->setData($res);
        return $result;
    }

    /**
     * @return User|null
     */
    private function getUser(): ?User
    {
        return $this->session->getUser();
    }

    /**
     * @inheritDoc
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
