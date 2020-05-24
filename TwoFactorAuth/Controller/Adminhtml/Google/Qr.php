<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Controller\Adminhtml\Google;

use Magento\Backend\Model\Auth\Session;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Controller\Adminhtml\AbstractAction;
use Magento\TwoFactorAuth\Model\Provider\Engine\Google;
use Magento\User\Model\User;

/**
 * QR code generator for google authenticator
 *
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Qr extends AbstractAction implements HttpGetActionInterface
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
     * @var Raw
     */
    private $rawResult;

    /**
     * @var Google
     */
    private $google;

    /**
     * @param Action\Context $context
     * @param Session $session
     * @param TfaInterface $tfa
     * @param Google $google
     * @param Raw $rawResult
     */
    public function __construct(
        Action\Context $context,
        Session $session,
        TfaInterface $tfa,
        Google $google,
        Raw $rawResult
    ) {
        parent::__construct($context);
        $this->tfa = $tfa;
        $this->session = $session;
        $this->rawResult = $rawResult;
        $this->google = $google;
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
     * @inheritDoc
     */
    public function execute()
    {
        $pngData = $this->google->getQrCodeAsPng($this->getUser());
        $this->rawResult
            ->setHttpResponseCode(200)
            ->setHeader('Content-Type', 'image/png')
            ->setContents($pngData);

        return $this->rawResult;
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        $user = $this->getUser();

        return
            $user &&
            $this->tfa->getProviderIsAllowed((int) $user->getId(), Google::CODE) &&
            !$this->tfa->getProvider(Google::CODE)->isActive((int) $user->getId());
    }
}
