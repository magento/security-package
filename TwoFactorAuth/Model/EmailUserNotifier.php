<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\User\Model\User;
use Magento\TwoFactorAuth\Api\Exception\NotificationExceptionInterface;
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
     * @var UrlInterface
     */
    private $url;

    /**
     * @var State
     */
    private $appState;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param UrlInterface $url
     * @param State $appState
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        UrlInterface $url,
        State $appState
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->url = $url;
        $this->appState = $appState;
    }

    /**
     * Send configuration related message to the admin user.
     *
     * @param User $user
     * @param string $token
     * @param string $emailTemplateId
     * @param bool $useWebApiUrl
     * @return void
     */
    private function sendConfigRequired(
        User $user,
        string $token,
        string $emailTemplateId,
        bool $useWebApiUrl = false
    ): void {
        try {
            $userUrl = $this->scopeConfig->getValue(TfaInterface::XML_PATH_WEBAPI_CONFIG_EMAIL_URL);
            if ($useWebApiUrl && $userUrl) {
                $url = $userUrl .
                    (parse_url($userUrl, PHP_URL_QUERY) ? '&' : '?') .
                    http_build_query(['tfat' => $token, 'user_id' => $user->getId()]);
            } else {
                $url = $this->url->getUrl('tfa/tfa/index', ['tfat' => $token]);
            }

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
        $this->sendConfigRequired($user, $token, 'tfa_admin_user_config_required', $this->isWebapi());
    }

    /**
     * @inheritDoc
     */
    public function sendAppConfigRequestMessage(User $user, string $token): void
    {
        $this->sendConfigRequired($user, $token, 'tfa_admin_app_config_required', false);
    }

    /**
     * Determine if the environment is webapi or not
     *
     * @return bool
     */
    private function isWebapi(): bool
    {
        return in_array($this->appState->getAreaCode(), [Area::AREA_WEBAPI_REST, Area::AREA_WEBAPI_SOAP]);
    }
}
