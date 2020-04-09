<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Plugin;

use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\Session as SessionManager;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

/**
 * Deletes the tfat cookie on logout
 */
class DeleteCookieOnLogout
{
    /**
     * @var CookieManagerInterface
     */
    private $cookies;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @param CookieManagerInterface $cookies
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param Session $session
     * @param SessionManager $sessionManager
     */
    public function __construct(
        CookieManagerInterface $cookies,
        CookieMetadataFactory $cookieMetadataFactory,
        Session $session,
        SessionManager $sessionManager
    ) {
        $this->cookies = $cookies;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->session = $session;
        $this->sessionManager = $sessionManager;
    }

    /**
     * Delete the tfat cookie
     */
    public function beforeLogout()
    {
        $metadata = $this->cookieMetadataFactory->createSensitiveCookieMetadata()
            ->setPath($this->sessionManager->getCookiePath());
        $this->cookies->deleteCookie('tfa-ct', $metadata);
    }
}
