<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine;

use Endroid\QrCode\Exception\ValidationException;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\User\Api\Data\UserInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use Magento\TwoFactorAuth\Api\EngineInterface;
use Base32\Base32;
use OTPHP\TOTP;

/**
 * Google authenticator engine
 */
class Google implements EngineInterface
{
    /**
     * Engine code
     */
    public const CODE = 'google'; // Must be the same as defined in di.xml

    /**
     * Configuration XML path for enabled flag
     */
    public const XML_PATH_ENABLED = 'twofactorauth/google/enabled';

    /**
     * Configuration XML path to allow trusted devices
     */
    public const XML_PATH_ALLOW_TRUSTED_DEVICES = 'twofactorauth/google/allow_trusted_devices';

    /**
     * @var null
     */
    private $totp = null;

    /**
     * @var UserConfigManagerInterface
     */
    private $configManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param UserConfigManagerInterface $configManager
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        UserConfigManagerInterface $configManager
    ) {
        $this->configManager = $configManager;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Generate random secret
     * @return string
     * @throws Exception
     */
    private function generateSecret(): string
    {
        $secret = random_bytes(128);
        return preg_replace('/[^A-Za-z0-9]/', '', Base32::encode($secret));
    }

    /**
     * Get TOTP object
     * @param UserInterface $user
     * @return TOTP
     * @throws NoSuchEntityException
     */
    private function getTotp(UserInterface $user): TOTP
    {
        if ($this->totp === null) {
            $config = $this->configManager->getProviderConfig((int) $user->getId(), static::CODE);

            if (!isset($config['secret'])) {
                $config['secret'] = $this->getSecretCode($user);
            }

            // @codingStandardsIgnoreStart
            $this->totp = new TOTP(
                $user->getEmail(),
                $config['secret']
            );
            // @codingStandardsIgnoreEnd
        }

        return $this->totp;
    }

    /**
     * Get the secret code used for Google Authentication
     * @param UserInterface $user
     * @return string|null
     * @throws NoSuchEntityException
     * @author Konrad Skrzynski <konrad.skrzynski@accenture.com>
     */
    public function getSecretCode(UserInterface $user): ?string
    {
        $config = $this->configManager->getProviderConfig((int) $user->getId(), static::CODE);

        if (!isset($config['secret'])) {
            $config['secret'] = $this->generateSecret();
            $this->configManager->setProviderConfig((int) $user->getId(), static::CODE, $config);
        }

        return $config['secret'] ?? null;
    }

    /**
     * Get TFA provisioning URL
     * @param UserInterface $user
     * @return string
     * @throws NoSuchEntityException
     */
    private function getProvisioningUrl(UserInterface $user): string
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();

        // @codingStandardsIgnoreStart
        $issuer = parse_url($baseUrl, PHP_URL_HOST);
        // @codingStandardsIgnoreEnd

        $totp = $this->getTotp($user);
        $totp->setIssuer($issuer);

        return $totp->getProvisioningUri();
    }

    /**
     * @inheritDoc
     */
    public function verify(UserInterface $user, DataObject $request): bool
    {
        $token = $request->getData('tfa_code');

        $totp = $this->getTotp($user);
        $totp->now();

        return $totp->verify($token);
    }

    /**
     * Render TFA QrCode
     * @param UserInterface $user
     * @return string
     * @throws NoSuchEntityException
     * @throws ValidationException
     */
    public function getQrCodeAsPng(UserInterface $user): string
    {
        // @codingStandardsIgnoreStart
        $qrCode = new QrCode($this->getProvisioningUrl($user));
        $qrCode->setSize(400);
        $qrCode->setErrorCorrectionLevel('high');
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
        $qrCode->setLabelFontSize(16);
        $qrCode->setEncoding('UTF-8');

        $writer = new PngWriter();
        $pngData = $writer->writeString($qrCode);
        // @codingStandardsIgnoreEnd

        return $pngData;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(static::XML_PATH_ENABLED);
    }

    /**
     * @inheritDoc
     */
    public function isTrustedDevicesAllowed(): bool
    {
        return (bool) $this->scopeConfig->getValue(static::XML_PATH_ALLOW_TRUSTED_DEVICES);
    }
}
