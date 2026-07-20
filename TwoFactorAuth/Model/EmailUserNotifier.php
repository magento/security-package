<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TwoFactorAuth\Model\Config\UserNotifier as UserNotifierConfig;
use Magento\User\Model\User;
use Magento\TwoFactorAuth\Api\UserNotifierInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\TwoFactorAuth\Model\Exception\NotificationException;
use Psr\Log\LoggerInterface;

/**
 * @inheritDoc
 */
class EmailUserNotifier implements UserNotifierInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var UserNotifierConfig
     */
    private $userNotifierConfig;

    /**
     * @var Emulation
     */
    private $appEmulation;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param UserNotifierConfig $userNotifierConfig
     * @param Emulation|null $appEmulation
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        UserNotifierConfig $userNotifierConfig,
        ?Emulation $appEmulation = null
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->userNotifierConfig = $userNotifierConfig;
        $this->appEmulation = $appEmulation
            ?? \Magento\Framework\App\ObjectManager::getInstance()->get(Emulation::class);
    }

    /**
     * Send configuration related message to the admin user.
     *
     * @param User $user
     * @param string $token
     * @param string $emailTemplateId
     * @param string $url
     * @return void
     */
    private function sendConfigRequired(
        User $user,
        string $token,
        string $emailTemplateId,
        string $url
    ): void {
        try {
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($emailTemplateId)
                ->setTemplateOptions([
                    'area' => 'adminhtml',
                    'store' => 0
                ])
                ->setTemplateVars(
                    [
                        'username' => $user->getFirstName() . ' ' . $user->getLastName(),
                        'token' => $token,
                        'store_name' => $this->storeManager->getStore()->getFrontendName(),
                        'url' => $url
                    ]
                )
                ->setFromByScope(
                    $this->scopeConfig->getValue('admin/emails/forgot_email_identity')
                )
                ->addTo($user->getEmail(), $user->getFirstName() . ' ' . $user->getLastName())
                ->getTransport();
            // The send-suppression check in TransportInterfacePlugin reads system/smtp/disable from
            // whatever store the environment currently resolves to, not from the store this email was
            // rendered for above. Emulate the default/admin store so it checks the same global scope
            // that this notification is actually governed by, rather than an unrelated frontend store.
            $this->appEmulation->startEnvironmentEmulation(Store::DEFAULT_STORE_ID, Area::AREA_ADMINHTML);
            try {
                $transport->sendMessage();
            } finally {
                $this->appEmulation->stopEnvironmentEmulation();
            }
        } catch (\Throwable $exception) {
            $this->logger->critical($exception);
            throw new NotificationException('Failed to send 2FA E-mail to a user', 0, $exception);
        }
    }

    /**
     * @inheritDoc
     */
    public function sendUserConfigRequestMessage(User $user, string $token): void
    {
        $this->sendConfigRequired(
            $user,
            $token,
            'tfa_admin_user_config_required',
            $this->userNotifierConfig->getPersonalRequestConfigUrl($token)
        );
    }

    /**
     * @inheritDoc
     */
    public function sendAppConfigRequestMessage(User $user, string $token): void
    {
        $this->sendConfigRequired(
            $user,
            $token,
            'tfa_admin_app_config_required',
            $this->userNotifierConfig->getAppRequestConfigUrl($token)
        );
    }
}
