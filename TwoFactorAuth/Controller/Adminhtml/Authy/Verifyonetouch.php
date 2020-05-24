<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Controller\Adminhtml\Authy;

use Exception;
use Magento\Backend\Model\Auth\Session;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\TwoFactorAuth\Model\AlertInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\TfaSessionInterface;
use Magento\TwoFactorAuth\Controller\Adminhtml\AbstractAction;
use Magento\TwoFactorAuth\Model\Provider\Engine\Authy;
use Magento\User\Model\User;

/**
 * Verify one touch response
 *
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Verifyonetouch extends AbstractAction implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var TfaSessionInterface
     */
    private $tfaSession;

    /**
     * @var AlertInterface
     */
    private $alert;

    /**
     * @var Authy\OneTouch
     */
    private $oneTouch;

    /**
     * Verifyonetouch constructor.
     * @param Action\Context $context
     * @param JsonFactory $jsonFactory
     * @param TfaSessionInterface $tfaSession
     * @param TfaInterface $tfa
     * @param AlertInterface $alert
     * @param Authy\OneTouch $oneTouch
     * @param Session $session
     */
    public function __construct(
        Action\Context $context,
        JsonFactory $jsonFactory,
        TfaSessionInterface $tfaSession,
        TfaInterface $tfa,
        AlertInterface $alert,
        Authy\OneTouch $oneTouch,
        Session $session
    ) {
        parent::__construct($context);
        $this->session = $session;
        $this->jsonFactory = $jsonFactory;
        $this->tfa = $tfa;
        $this->tfaSession = $tfaSession;
        $this->alert = $alert;
        $this->oneTouch = $oneTouch;
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
     * @inheritdoc
     */
    public function execute()
    {
        $result = $this->jsonFactory->create();

        try {
            $res = $this->oneTouch->verify($this->getUser());
            if ($res === 'approved') {
                $this->tfaSession->grantAccess();
                $res = ['success' => true, 'status' => 'approved'];
            } else {
                $res = ['success' => false, 'status' => $res];

                if ($res === 'denied') {
                    $this->alert->event(
                        'Magento_TwoFactorAuth',
                        'Authy onetouch auth denied',
                        AlertInterface::LEVEL_WARNING,
                        $this->getUser()->getUserName()
                    );
                }
            }
        } catch (Exception $e) {
            $result->setHttpResponseCode(500);
            $res = ['success' => false, 'message' => $e->getMessage()];

            $this->alert->event(
                'Magento_TwoFactorAuth',
                'Authy onetouch error',
                AlertInterface::LEVEL_ERROR,
                $this->getUser()->getUserName(),
                $e->getMessage()
            );
        }

        $result->setData($res);
        return $result;
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
            $this->tfa->getProvider(Authy::CODE)->isActive((int) $user->getId());
    }
}
