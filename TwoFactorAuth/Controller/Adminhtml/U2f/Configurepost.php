<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Controller\Adminhtml\U2f;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\TwoFactorAuth\Model\AlertInterface;
use Magento\TwoFactorAuth\Api\TfaSessionInterface;
use Magento\TwoFactorAuth\Controller\Adminhtml\AbstractConfigureAction;
use Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey;
use Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey\Session as U2fSession;
use Magento\TwoFactorAuth\Model\Tfa;
use Magento\User\Model\User;
use Magento\TwoFactorAuth\Model\UserConfig\HtmlAreaTokenVerifier;

/**
 * U2f key configuration post controller
 *
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Configurepost extends AbstractConfigureAction implements HttpPostActionInterface
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
     * @var U2fSession
     */
    private $u2fSession;

    /**
     * @param Tfa $tfa
     * @param Session $session
     * @param JsonFactory $jsonFactory
     * @param TfaSessionInterface $tfaSession
     * @param U2fSession $u2fSession
     * @param U2fKey $u2fKey
     * @param AlertInterface $alert
     * @param Context $context
     * @param HtmlAreaTokenVerifier $tokenVerifier
     */
    public function __construct(
        Tfa $tfa,
        Session $session,
        JsonFactory $jsonFactory,
        TfaSessionInterface $tfaSession,
        U2fSession $u2fSession,
        U2fKey $u2fKey,
        AlertInterface $alert,
        Context $context,
        HtmlAreaTokenVerifier $tokenVerifier
    ) {
        parent::__construct($context, $tokenVerifier);

        $this->tfa = $tfa;
        $this->session = $session;
        $this->u2fKey = $u2fKey;
        $this->jsonFactory = $jsonFactory;
        $this->tfaSession = $tfaSession;
        $this->alert = $alert;
        $this->u2fSession = $u2fSession;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = $this->jsonFactory->create();

        try {
            $challenge = $this->u2fSession->getU2fChallenge();
            if (!empty($challenge)) {
                $data = [
                   'publicKeyCredential' => $this->getRequest()->getParam('publicKeyCredential'),
                   'challenge' => $challenge
                ];

                $this->u2fKey->registerDevice($this->getUser(), $data);
                $this->tfaSession->grantAccess();
                $this->u2fSession->setU2fChallenge(null);

                $this->alert->event(
                    'Magento_TwoFactorAuth',
                    'U2F New device registered',
                    AlertInterface::LEVEL_INFO,
                    $this->getUser()->getUserName()
                );
                $res = ['success' => true];
            } else {
                $res = ['success' => false];
            }

        } catch (\Throwable $e) {
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
     * Get the current user
     *
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
        if (!parent::_isAllowed()) {
            return false;
        }

        $user = $this->getUser();

        return
            $user &&
            $this->tfa->getProviderIsAllowed((int) $user->getId(), U2fKey::CODE) &&
            !$this->tfa->getProvider(U2fKey::CODE)->isActive((int) $user->getId());
    }
}
