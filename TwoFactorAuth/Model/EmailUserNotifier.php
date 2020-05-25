<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
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
     * @param ScopeConfigInterface $scopeConfig
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param UserNotifierConfig $userNotifierConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        UserNotifierConfig $userNotifierConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->userNotifierConfig = $userNotifierConfig;
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
            $transport->sendMessage();
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
