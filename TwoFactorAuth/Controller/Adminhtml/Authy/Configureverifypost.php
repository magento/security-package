<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Controller\Adminhtml\Authy;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\TwoFactorAuth\Model\AlertInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\TfaSessionInterface;
use Magento\TwoFactorAuth\Controller\Adminhtml\AbstractConfigureAction;
use Magento\TwoFactorAuth\Model\Provider\Engine\Authy;
use Magento\User\Model\User;
use Magento\TwoFactorAuth\Model\UserConfig\HtmlAreaTokenVerifier;

/**
 * Verify authy
 *
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Configureverifypost extends AbstractConfigureAction implements HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var Authy
     */
    private $authy;

    /**
     * @var TfaSessionInterface
     */
    private $tfaSession;

    /**
     * @var AlertInterface
     */
    private $alert;

    /**
     * @var Authy\Verification
     */
    private $verification;

    /**
     * @param Action\Context $context
     * @param Session $session
     * @param TfaInterface $tfa
     * @param TfaSessionInterface $tfaSession
     * @param AlertInterface $alert
     * @param Authy $authy
     * @param Authy\Verification $verification
     * @param JsonFactory $jsonFactory
     * @param HtmlAreaTokenVerifier $tokenVerifier
     */
    public function __construct(
        Action\Context $context,
        Session $session,
        TfaInterface $tfa,
        TfaSessionInterface $tfaSession,
        AlertInterface $alert,
        Authy $authy,
        Authy\Verification $verification,
        JsonFactory $jsonFactory,
        HtmlAreaTokenVerifier $tokenVerifier
    ) {
        parent::__construct($context, $tokenVerifier);
        $this->jsonFactory = $jsonFactory;
        $this->session = $session;
        $this->tfa = $tfa;
        $this->tfaSession = $tfaSession;
        $this->alert = $alert;
        $this->verification = $verification;
        $this->authy = $authy;
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
        $verificationCode = $this->getRequest()->getParam('tfa_verify');
        $response = $this->jsonFactory->create();

        try {
            $this->verification->verify($this->getUser(), $verificationCode);
            $this->authy->enroll($this->getUser());
            $this->tfaSession->grantAccess();

            $this->alert->event(
                'Magento_TwoFactorAuth',
                'Authy identity verified',
                AlertInterface::LEVEL_INFO,
                $this->getUser()->getUserName()
            );

            $response->setData([
                'success' => true,
            ]);
        } catch (Exception $e) {
            $this->alert->event(
                'Magento_TwoFactorAuth',
                'Authy identity verification failure',
                AlertInterface::LEVEL_ERROR,
                $this->getUser()->getUserName(),
                $e->getMessage()
            );

            $response->setData([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

        return $response;
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        if (!parent::_isAllowed()) {
            return false;
        }

        $user = $this->getUser();

        return
            $user &&
            $this->tfa->getProviderIsAllowed((int) $user->getId(), Authy::CODE) &&
            !$this->tfa->getProvider(Authy::CODE)->isActive((int) $user->getId());
    }
}
