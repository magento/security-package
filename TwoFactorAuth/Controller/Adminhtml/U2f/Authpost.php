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
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\DataObjectFactory;
use Magento\TwoFactorAuth\Model\AlertInterface;
use Magento\TwoFactorAuth\Api\TfaSessionInterface;
use Magento\TwoFactorAuth\Controller\Adminhtml\AbstractAction;
use Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey;
use Magento\TwoFactorAuth\Model\Tfa;
use Magento\User\Model\User;

/**
 * U2f key Authentication post controller
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Authpost extends AbstractAction implements HttpPostActionInterface
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
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var AlertInterface
     */
    private $alert;

    public function __construct(
        Tfa $tfa,
        Session $session,
        JsonFactory $jsonFactory,
        TfaSessionInterface $tfaSession,
        U2fKey $u2fKey,
        DataObjectFactory $dataObjectFactory,
        AlertInterface $alert,
        Action\Context $context
    ) {
        parent::__construct($context);

        $this->tfa = $tfa;
        $this->session = $session;
        $this->u2fKey = $u2fKey;
        $this->jsonFactory = $jsonFactory;
        $this->tfaSession = $tfaSession;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->alert = $alert;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = $this->jsonFactory->create();

        try {
            $challenge = $this->session->getTfaU2fChallenge();
            if (!empty($challenge)) {
                $this->u2fKey->verify($this->getUser(), $this->dataObjectFactory->create([
                    'data' => [
                        'publicKeyCredential' => $this->getRequest()->getParams()['publicKeyCredential'],
                        'originalChallenge' => $challenge
                    ]
                ]));
                $this->tfaSession->grantAccess();
                $this->session->unsTfaU2fChallenge();

                $res = ['success' => true];
            } else {
                $res = ['success' => false];
            }
        } catch (Exception $e) {
            $this->alert->event(
                'Magento_TwoFactorAuth',
                'U2F error',
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
     * Check if admin has permissions to visit related pages
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        $user = $this->getUser();

        return
            $user &&
            $this->tfa->getProviderIsAllowed((int) $user->getId(), U2fKey::CODE) &&
            $this->tfa->getProvider(U2fKey::CODE)->isActive((int) $user->getId());
    }
}
