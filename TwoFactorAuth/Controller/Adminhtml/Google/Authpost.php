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
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\User\Model\ResourceModel\User as UserResource;

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
     * Config path for the 2FA Attempts
     */
    private const XML_PATH_2FA_RETRY_ATTEMPTS = 'twofactorauth/general/twofactorauth_retry';

    /**
     * Config path for the 2FA Attempts
     */
    private const XML_PATH_2FA_LOCK_EXPIRE = 'twofactorauth/general/auth_lock_expire';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var UserResource
     */
    protected $userResource;

    /**
     * @param Action\Context $context
     * @param Session $session
     * @param JsonFactory $jsonFactory
     * @param Google $google
     * @param TfaSessionInterface $tfaSession
     * @param TfaInterface $tfa
     * @param AlertInterface $alert
     * @param DataObjectFactory $dataObjectFactory
     * @param UserResource $userResource
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Action\Context $context,
        Session $session,
        JsonFactory $jsonFactory,
        Google $google,
        TfaSessionInterface $tfaSession,
        TfaInterface $tfa,
        AlertInterface $alert,
        DataObjectFactory $dataObjectFactory,
        UserResource $userResource,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->tfa = $tfa;
        $this->session = $session;
        $this->jsonFactory = $jsonFactory;
        $this->google = $google;
        $this->tfaSession = $tfaSession;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->alert = $alert;
        $this->userResource = $userResource;
        $this->scopeConfig = $scopeConfig;
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

        $maxRetries = $this->scopeConfig->getValue(self::XML_PATH_2FA_RETRY_ATTEMPTS);
        $retries = $this->verifyRetryAttempts();
        if ($retries > $maxRetries) { //locked the user
            $lockThreshold = $this->scopeConfig->getValue(self::XML_PATH_2FA_LOCK_EXPIRE);
            if ($this->userResource->lock($user->getId(),0, $lockThreshold)) {
                $response->setData(['success' => false, 'message' => "User is disabled temporarily!"]);
            }
        } else {
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

    /**
     * Get retry attempt count
     *
     * @return int
     */
    private function verifyRetryAttempts() : int
    {
        $verifyAttempts = $this->session->getOtpAttempt();
        $verifyAttempts = is_null($verifyAttempts) ? 0 : $verifyAttempts+1;
        $this->session->setOtpAttempt($verifyAttempts);
        return $verifyAttempts;
    }
}
