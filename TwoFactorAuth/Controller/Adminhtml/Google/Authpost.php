<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Controller\Adminhtml\Google;

use Magento\Backend\Model\Auth\Session;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TwoFactorAuth\Model\AlertInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\TfaSessionInterface;
use Magento\TwoFactorAuth\Controller\Adminhtml\AbstractAction;
use Magento\TwoFactorAuth\Model\Provider\Engine\Google;
use Magento\User\Model\User;

/**
 * Google authenticator post controller
 *
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
     * @var JsonFactory
     */
    private $jsonFactory;
    /**
     * @var Google
     */
    private $google;

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

    /**
     * @param Action\Context $context
     * @param Session $session
     * @param JsonFactory $jsonFactory
     * @param Google $google
     * @param TfaSessionInterface $tfaSession
     * @param TfaInterface $tfa
     * @param AlertInterface $alert
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        Action\Context $context,
        Session $session,
        JsonFactory $jsonFactory,
        Google $google,
        TfaSessionInterface $tfaSession,
        TfaInterface $tfa,
        AlertInterface $alert,
        DataObjectFactory $dataObjectFactory
    ) {
        parent::__construct($context);
        $this->tfa = $tfa;
        $this->session = $session;
        $this->jsonFactory = $jsonFactory;
        $this->google = $google;
        $this->tfaSession = $tfaSession;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->alert = $alert;
    }

    /**
     * @inheritdoc
     *
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $user = $this->session->getUser();
        $response = $this->jsonFactory->create();
        /** @var \Magento\Framework\DataObject $request */
        $request = $this->dataObjectFactory->create(['data' => $this->getRequest()->getParams()]);

        if ($this->google->verify($user, $request)) {
            $this->tfaSession->grantAccess();
            $response->setData(['success' => true]);
        } else {
            $this->alert->event(
                'Magento_TwoFactorAuth',
                'Google auth invalid token',
                AlertInterface::LEVEL_WARNING,
                $user->getUserName()
            );

            $response->setData(['success' => false, 'message' => 'Invalid code']);
        }

        return $response;
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        $user = $this->session->getUser();

        return $user
            && $this->tfa->getProviderIsAllowed((int)$user->getId(), Google::CODE)
            && $this->tfa->getProvider(Google::CODE)->isActive((int)$user->getId());
    }
}
