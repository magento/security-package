<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Plugin;

use Magento\Backend\Model\Session as SessionManager;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

/**
 * Deletes the tfat cookie on logout
 *
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
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
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @param CookieManagerInterface $cookies
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param SessionManager $sessionManager
     */
    public function __construct(
        CookieManagerInterface $cookies,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManager $sessionManager
    ) {
        $this->cookies = $cookies;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionManager = $sessionManager;
    }

    /**
     * Delete the tfat cookie
     *
     * @param \Magento\Backend\Model\Auth $subject
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeLogout(\Magento\Backend\Model\Auth $subject)
    {
        $metadata = $this->cookieMetadataFactory->createSensitiveCookieMetadata()
            ->setPath($this->sessionManager->getCookiePath());
        $this->cookies->deleteCookie('tfa-ct', $metadata);
    }
}
