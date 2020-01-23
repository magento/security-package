<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\User\Model\User;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\TrustedManagerInterface;
use Magento\TwoFactorAuth\Api\TrustedRepositoryInterface;
use Magento\TwoFactorAuth\Model\ResourceModel\Trusted as TrustedResourceModel;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;

/**
 * @inheritDoc
 */
class TrustedManager implements TrustedManagerInterface
{
    private $isTrustedDevice = null;

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var TrustedFactory
     */
    private $trustedFactory;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var TrustedResourceModel
     */
    private $trustedResourceModel;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var TrustedRepositoryInterface
     */
    private $trustedRepository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param TfaInterface $tfa
     * @param DateTime $dateTime
     * @param Session $session
     * @param RemoteAddress $remoteAddress
     * @param SerializerInterface $serializer
     * @param TrustedResourceModel $trustedResourceModel
     * @param CookieManagerInterface $cookieManager
     * @param SessionManagerInterface $sessionManager
     * @param TrustedRepositoryInterface $trustedRepository
     * @param TrustedFactory $trustedFactory
     * @param CookieMetadataFactory $cookieMdFactory
     * @SuppressWarnings("PHPMD.ExcessiveParameterList")
     */
    public function __construct(
        TfaInterface $tfa,
        DateTime $dateTime,
        Session $session,
        RemoteAddress $remoteAddress,
        SerializerInterface $serializer,
        TrustedResourceModel $trustedResourceModel,
        CookieManagerInterface $cookieManager,
        SessionManagerInterface $sessionManager,
        TrustedRepositoryInterface $trustedRepository,
        TrustedFactory $trustedFactory,
        CookieMetadataFactory $cookieMdFactory
    ) {
        $this->tfa = $tfa;
        $this->trustedFactory = $trustedFactory;
        $this->dateTime = $dateTime;
        $this->remoteAddress = $remoteAddress;
        $this->session = $session;
        $this->trustedResourceModel = $trustedResourceModel;
        $this->cookieManager = $cookieManager;
        $this->sessionManager = $sessionManager;
        $this->cookieMetadataFactory = $cookieMdFactory;
        $this->trustedRepository = $trustedRepository;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    private function getUser(): User
    {
        return $this->session->getUser();
    }

    /**
     * @inheritDoc
     */
    private function getDeviceName(): string
    {
        $browser = parse_user_agent();
        return $browser['platform'] . ' ' . $browser['browser'] . ' ' . $browser['version'];
    }

    /**
     * @inheritDoc
     */
    private function getTokenCollection(): array
    {
        try {
            return $this->serializer->unserialize(
                $this->cookieManager->getCookie(TrustedManagerInterface::TRUSTED_DEVICE_COOKIE)
            );
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @inheritDoc
     */
    private function sendTokenCookie(string $token): void
    {
        $user = $this->getUser();
        $tokenCollection = $this->getTokenCollection();

        // Enable cookie
        $cookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setDurationOneYear()
            ->setHttpOnly(true)
            ->setPath($this->sessionManager->getCookiePath())
            ->setDomain($this->sessionManager->getCookieDomain());

        $tokenCollection[$user->getUserName()] = $token;

        $this->cookieManager->setPublicCookie(
            TrustedManagerInterface::TRUSTED_DEVICE_COOKIE,
            $this->serializer->serialize($tokenCollection),
            $cookieMetadata
        );
    }

    /**
     * @inheritDoc
     */
    public function rotateTrustedDeviceToken(): void
    {
        $user = $this->getUser();
        $tokenCollection = $this->getTokenCollection();

        if (isset($tokenCollection[$user->getUserName()])) {
            $token = $tokenCollection[$user->getUserName()];

            /** @var Trusted $trustEntry */
            $trustEntry = $this->trustedFactory->create();
            $this->trustedResourceModel->load($trustEntry, $token, 'token');
            if ($trustEntry->getId() && ((int) $trustEntry->getUserId() === (int) $user->getId())) {
                $token = sha1(uniqid((string) time(), true));

                $trustEntry->setToken($token);
                $this->trustedResourceModel->save($trustEntry);

                $this->sendTokenCookie($token);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function isTrustedDevice(): bool
    {
        if ($this->isTrustedDevice === null) { // Must cache this in a single session to avoid rotation issues
            $user = $this->getUser();
            $tokenCollection = $this->getTokenCollection();

            if (isset($tokenCollection[$user->getUserName()])) {
                $token = $tokenCollection[$user->getUserName()];

                /** @var $trustEntry Trusted */
                $trustEntry = $this->trustedFactory->create();
                $this->trustedResourceModel->load($trustEntry, $token, 'token');

                $this->isTrustedDevice = $trustEntry->getId() &&
                    ((int) $trustEntry->getUserId() === (int) $user->getId());
            } else {
                $this->isTrustedDevice = false;
            }
        }

        return $this->isTrustedDevice;
    }

    /**
     * @inheritDoc
     */
    public function revokeTrustedDevice(int $tokenId): void
    {
        $token = $this->trustedRepository->getById($tokenId);
        $this->trustedRepository->delete($token);
    }

    /**
     * @inheritDoc
     */
    public function handleTrustDeviceRequest(string $providerCode, RequestInterface $request): bool
    {
        $provider = $this->tfa->getProvider($providerCode);
        if ($provider) {
            if ($provider->isTrustedDevicesAllowed() &&
                $request->getParam('tfa_trust_device') &&
                ($request->getParam('tfa_trust_device') !== 'false') // u2fkey submit translates into a string
            ) {
                $token = sha1(uniqid((string) time(), true));

                /** @var Trusted $trustEntry */
                $trustEntry = $this->trustedFactory->create();
                $trustEntry->setToken($token);
                $trustEntry->setDateTime($this->dateTime->date());
                $trustEntry->setUserId((int) $this->getUser()->getId());
                $trustEntry->setLastIp($this->remoteAddress->getRemoteAddress());
                $trustEntry->setDeviceName($this->getDeviceName());
                $trustEntry->setUserAgent($request->getServer('HTTP_USER_AGENT'));

                $this->trustedResourceModel->save($trustEntry);

                $this->sendTokenCookie($token);
                return true;
            }
        }

        return false;
    }
}
