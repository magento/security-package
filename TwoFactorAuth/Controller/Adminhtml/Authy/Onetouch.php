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
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Controller\Adminhtml\AbstractAction;
use Magento\TwoFactorAuth\Model\Provider\Engine\Authy;
use Magento\User\Model\User;

/**
 * One touch process
 *
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Onetouch extends AbstractAction implements HttpGetActionInterface
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
     * @var Authy\OneTouch
     */
    private $oneTouch;

    /**
     * @param Action\Context $context
     * @param JsonFactory $jsonFactory
     * @param TfaInterface $tfa
     * @param Authy\OneTouch $oneTouch
     * @param Session $session
     */
    public function __construct(
        Action\Context $context,
        JsonFactory $jsonFactory,
        TfaInterface $tfa,
        Authy\OneTouch $oneTouch,
        Session $session
    ) {
        parent::__construct($context);
        $this->session = $session;
        $this->jsonFactory = $jsonFactory;
        $this->tfa = $tfa;
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
            $this->oneTouch->request($this->getUser());
            $res = ['success' => true];
        } catch (Exception $e) {
            $result->setHttpResponseCode(500);
            $res = ['success' => false, 'message' => $e->getMessage()];
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
