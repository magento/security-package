<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Exception\ValidationException;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Google\TotpFactory;
use Magento\User\Api\Data\UserInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use Magento\TwoFactorAuth\Api\EngineInterface;
use Base32\Base32;
use OTPHP\TOTPInterface;

/**
 * Google authenticator engine
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Google implements EngineInterface
{
    /**
     * Config path for the OTP window
     */
    const XML_PATH_OTP_WINDOW = 'twofactorauth/google/otp_window';

    /**
     * Engine code
     *
     * Must be the same as defined in di.xml
     */
    public const CODE = 'google';

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
     * @var TOTPInterfaceFactory
     */
    private $totpFactory;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param UserConfigManagerInterface $configManager
     * @param TotpFactory $totpFactory
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        UserConfigManagerInterface $configManager,
        TotpFactory $totpFactory,
        EncryptorInterface $encryptor
    ) {
        $this->configManager = $configManager;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->totpFactory = $totpFactory;
        $this->encryptor = $encryptor;
    }

    /**
     * Generate random secret
     *
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
     *
     * @param UserInterface $user
     * @return TOTPInterface
     * @throws NoSuchEntityException
     */
    private function getTotp(UserInterface $user): TOTPInterface
    {
        $secret = $this->getSecretCode($user);

        if (!$secret) {
            throw new NoSuchEntityException(__('Secret for user with ID#%1 was not found', $user->getId()));
        }

        $totp = $this->totpFactory->create($secret);

        return $totp;
    }

    /**
     * Get the secret code used for Google Authentication
     *
     * @param UserInterface $user
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getSecretCode(UserInterface $user): ?string
    {
        $config = $this->configManager->getProviderConfig((int)$user->getId(), static::CODE);

        if (!isset($config['secret'])) {
            $config['secret'] = $this->generateSecret();
            $this->setSharedSecret((int)$user->getId(), $config['secret']);
            return $config['secret'];
        }

        return $config['secret'] ? $this->encryptor->decrypt($config['secret']) : null;
    }

    /**
     * Set the secret used to generate OTP
     *
     * @param int $userId
     * @param string $secret
     * @throws NoSuchEntityException
     */
    public function setSharedSecret(int $userId, string $secret): void
    {
        $this->configManager->addProviderConfig(
            $userId,
            static::CODE,
            ['secret' => $this->encryptor->encrypt($secret)]
        );
    }

    /**
     * Get TFA provisioning URL
     *
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
        $totp->setLabel($user->getEmail());
        $totp->setIssuer($issuer);

        return $totp->getProvisioningUri();
    }

    /**
     * @inheritDoc
     */
    public function verify(UserInterface $user, DataObject $request): bool
    {
        $token = $request->getData('tfa_code');
        if (!$token) {
            return false;
        }

        $totp = $this->getTotp($user);
        $config = $this->configManager->getProviderConfig((int)$user->getId(), static::CODE);

        return $totp->verify(
            $token,
            null,
            $config['window'] ?? (int)$this->scopeConfig->getValue(self::XML_PATH_OTP_WINDOW) ?: null
        );
    }

    /**
     * Render TFA QrCode
     *
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
        $qrCode->setMargin(0);
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH());
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
        return true;
    }
}
