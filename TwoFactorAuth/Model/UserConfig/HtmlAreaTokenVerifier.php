<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\UserConfig;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\TwoFactorAuth\Api\UserConfigTokenManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Backend\Model\Session as SessionManager;

/**
 * Finds and verifies token allowing users to configure 2FA.
 *
 * Works for adminhtml area.
 *
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class HtmlAreaTokenVerifier
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var UserConfigTokenManagerInterface
     */
    private $tokenManager;

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
     * @param RequestInterface $request
     * @param UserConfigTokenManagerInterface $tokenManager
     * @param CookieManagerInterface $cookies
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param Session $session
     * @param SessionManager $sessionManager
     */
    public function __construct(
        RequestInterface $request,
        UserConfigTokenManagerInterface $tokenManager,
        CookieManagerInterface $cookies,
        CookieMetadataFactory $cookieMetadataFactory,
        Session $session,
        SessionManager $sessionManager
    ) {
        $this->request = $request;
        $this->tokenManager = $tokenManager;
        $this->cookies = $cookies;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->session = $session;
        $this->sessionManager = $sessionManager;
    }

    /**
     * Was config token provided by current user?.
     *
     * @return bool
     */
    public function isConfigTokenProvided(): bool
    {
        return (bool)$this->readConfigToken();
    }

    /**
     * Read configuration token provided by user.
     *
     * @return string|null
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function readConfigToken(): ?string
    {
        $user = $this->session->getUser();
        if (!$user) {
            return null;
        }
        $cookieToken = $this->cookies->getCookie('tfa-ct');
        $paramToken = $this->request->getParam('tfat');
        $cookieTokenValid = $cookieToken && $this->tokenManager->isValidFor((int)$user->getId(), $cookieToken);
        $paramTokenValid = $paramToken && $this->tokenManager->isValidFor((int)$user->getId(), $paramToken);

        if (!$cookieTokenValid && !$paramTokenValid) {
            return null;
        } elseif ($paramTokenValid && !$cookieTokenValid) {
            $metadata = $this->cookieMetadataFactory->createSensitiveCookieMetadata()
                ->setPath($this->sessionManager->getCookiePath());
            $this->cookies->setSensitiveCookie('tfa-ct', $paramToken, $metadata);
            return $paramToken;
        } elseif (!$paramTokenValid && $cookieTokenValid) {
            return $cookieToken;
        } else {
            return $cookieToken;
        }
    }
}
