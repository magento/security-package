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
use Magento\Framework\View\Result\PageFactory;
use Magento\TwoFactorAuth\Model\AlertInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Controller\Adminhtml\AbstractConfigureAction;
use Magento\TwoFactorAuth\Model\Provider\Engine\Authy;
use Magento\User\Model\User;
use Magento\TwoFactorAuth\Model\UserConfig\HtmlAreaTokenVerifier;

/**
 * Configure authy
 *
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Configurepost extends AbstractConfigureAction implements HttpPostActionInterface
{
    /**
     * @var PageFactory
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
     * @param Authy\Verification $verification
     * @param TfaInterface $tfa
     * @param AlertInterface $alert
     * @param JsonFactory $jsonFactory
     * @param HtmlAreaTokenVerifier $tokenVerifier
     */
    public function __construct(
        Action\Context $context,
        Session $session,
        Authy\Verification $verification,
        TfaInterface $tfa,
        AlertInterface $alert,
        JsonFactory $jsonFactory,
        HtmlAreaTokenVerifier $tokenVerifier
    ) {
        parent::__construct($context, $tokenVerifier);
        $this->jsonFactory = $jsonFactory;
        $this->session = $session;
        $this->tfa = $tfa;
        $this->alert = $alert;
        $this->verification = $verification;
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
        $request = $this->getRequest();
        $response = $this->jsonFactory->create();

        try {
            $res = [];
            $this->verification->request(
                $this->getUser(),
                (string) $request->getParam('tfa_country'),
                (string) $request->getParam('tfa_phone'),
                (string) $request->getParam('tfa_method'),
                $res
            );

            $this->alert->event(
                'Magento_TwoFactorAuth',
                'New authy verification request via ' . $request->getParam('tfa_method'),
                AlertInterface::LEVEL_INFO,
                $this->getUser()->getUserName()
            );

            $response->setData([
                'success' => true,
                'message' => $res['message'],
                'seconds_to_expire' => (int) $res['seconds_to_expire'],
            ]);
        } catch (Exception $e) {
            $this->alert->event(
                'Magento_TwoFactorAuth',
                'Authy verification request failure via ' . $request->getParam('tfa_method'),
                AlertInterface::LEVEL_ERROR,
                $this->getUser()->getUserName(),
                $e->getMessage()
            );
            $response->setData(['success' => false, 'message' => $e->getMessage()]);
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
