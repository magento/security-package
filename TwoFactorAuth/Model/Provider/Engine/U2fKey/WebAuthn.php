<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey;

use CBOR\CBOREncoder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validation\ValidationException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TwoFactorAuth\Api\U2fKeyConfigReaderInterface;
use Magento\User\Api\Data\UserInterface;

/**
 * Basic behavior for WebAuthn operations
 */
class WebAuthn
{
    /**
     * @see https://tools.ietf.org/html/rfc8152
     */
    private const ES256 = -7;

    private const PUBKEY_LEN = 65;

    /**
     * @var U2fKeyConfigReaderInterface
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     * @param U2fKeyConfigReaderInterface $u2fKeyConfig
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        U2fKeyConfigReaderInterface $u2fKeyConfig
    ) {
        $this->config = $u2fKeyConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Analyze a PublicKeyCredential object and verify it is valid
     *
     * @param array $credentialData
     * @param array $publicKeys
     * @param array $originalChallenge
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function assertCredentialDataIsValid(
        array $credentialData,
        array $publicKeys,
        array $originalChallenge
    ): void {
        // Verification process as defined by w3 https://www.w3.org/TR/webauthn/#verifying-assertion

        // Step 1-3
        $key = false;
        foreach ($publicKeys as $registeredKey) {
            if ($registeredKey['id'] === $credentialData['id']) {
                $key = $registeredKey;
                break;
            }
        }

        if (empty($key)) {
            throw new LocalizedException(__('Invalid U2F key.'));
        }

        $domain = $this->config->getDomain();

        // Steps 7-9
        if (rtrim(strtr(base64_encode($this->convertArrayToBytes($originalChallenge)), '+/', '-_'), '=')
            !== $credentialData['response']['clientData']['challenge']
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            || $domain !== parse_url($credentialData['response']['clientData']['origin'], \PHP_URL_HOST)
            || $credentialData['response']['clientData']['type'] !== 'webauthn.get'
        ) {
            throw new LocalizedException(__('Invalid U2F key.'));
        }

        // Step 10 not applicable

        // @see https://www.w3.org/TR/webauthn/#sec-authenticator-data
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $authenticatorDataBytes = base64_decode($credentialData['response']['authenticatorData']);
        $attestationObject = [
            'rpIdHash' => substr($authenticatorDataBytes, 0, 32),
            'flags' => ord(substr($authenticatorDataBytes, 32, 1)),
            'counter' => substr($authenticatorDataBytes, 33, 4),
        ];

        // Steps 11-12 (skipping 13 due to some devices failing to set the flags correctly)
        $hashId = hash('sha256', $domain, true);
        if ($hashId !== $attestationObject['rpIdHash']
            || !($attestationObject['flags'] & 0b1)
        ) {
            throw new LocalizedException(__('Invalid U2F key.'));
        }

        // Steps 15-16
        $clientDataSha256 = hash('sha256', $credentialData['response']['clientDataJSON'], true);
        $isValidSignature = openssl_verify(
            $authenticatorDataBytes . $clientDataSha256,
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            base64_decode($credentialData['response']['signature']),
            $key['key'],
            OPENSSL_ALGO_SHA256
        );
        if (!$isValidSignature) {
            throw new LocalizedException(__('Invalid U2F key.'));
        }

        // Skipping step 17 per the spec. This is sufficient proof for us at this point.
    }

    /**
     * Get all data needed for an authentication prompt
     *
     * @param array $publicKeys
     * @return array
     * @throws LocalizedException
     */
    public function getAuthenticateData(array $publicKeys): array
    {
        try {
            $challenge = random_bytes(16);
        } catch (\Exception $e) {
            throw new LocalizedException(__('There was an error during the U2F key process.'));
        }

        $store = $this->storeManager->getStore(Store::ADMIN_CODE);
        $allowedCredentials = [];
        foreach ($publicKeys as $key) {
            $allowedCredentials[] = [
                'type' => 'public-key',
                // phpcs:ignore Magento2.Functions.DiscouragedFunction
                'id' => $this->convertBytesToArray(base64_decode($key['id']))
            ];
        }

        $data = [
            'credentialRequestOptions' => [
                'challenge' => $this->convertBytesToArray($challenge),
                'timeout' => 60000,
                'allowCredentials' => $allowedCredentials,
                'userVerification' => 'discouraged',
                'extensions' => [
                    'txAuthSimple' => 'Authenticate with ' . $store->getName(),
                ],
                'rpId' => $this->config->getDomain(),
            ]
        ];

        return $data;
    }

    /**
     * Generate the challenge for registration
     *
     * @param UserInterface $user
     * @return array
     * @throws LocalizedException
     */
    public function getRegisterData(UserInterface $user): array
    {
        $domain = $this->config->getDomain();

        try {
            $challenge = random_bytes(16);
        } catch (\Exception $e) {
            throw new LocalizedException(__('There was an error during the U2F key process.'));
        }
        $data = [
            'publicKey' => [
                'challenge' => $this->convertBytesToArray($challenge),
                'user' => [
                    'id' => $this->convertBytesToArray(sha1($user->getId())),
                    'name' => $user->getUserName(),
                    'displayName' => $user->getUserName()
                ],
                'rp' => [
                    'name' => $domain,
                    'id' => $domain,
                ],
                'pubKeyCredParams' => [
                    [
                        'alg' => self::ES256,
                        'type' => 'public-key'
                    ],
                ],
                'attestation' => 'indirect',
                'authenticatorSelection' => [
                    'authenticatorAttachment' => 'cross-platform',
                    'requireResidentKey' => false,
                    'userVerification' => 'discouraged'
                ],
                'timeout' => 60000,
                // Currently only one device may be registered at a time
                'excludeCredentials' => [],
                'extensions' => [
                    'exts' => true
                ]
            ]
        ];

        return $data;
    }

    /**
     * Convert registration data response into public key
     *
     * @param array $data
     * @return array
     * @throws ValidationException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getPublicKeyFromRegistrationData(array $data): array
    {
        // Verification process as defined by w3 @see https://www.w3.org/TR/webauthn/#registering-a-new-credential

        $credentialData = $data['publicKeyCredential'];
        $domain = $this->config->getDomain();

        if (rtrim(strtr(base64_encode($this->convertArrayToBytes($data['challenge'])), '+/', '-_'), '=')
            !== $credentialData['response']['clientData']['challenge']
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            || $domain !== parse_url($credentialData['response']['clientData']['origin'], \PHP_URL_HOST)
            || $credentialData['response']['clientData']['type'] !== 'webauthn.create'
        ) {
            throw new LocalizedException(__('Invalid U2F key.'));
        }

        if (empty($credentialData['response']['attestationObject']) || empty($credentialData['id'])) {
            throw new ValidationException(__('Invalid U2F key data'));
        }
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $byteString = base64_decode($credentialData['response']['attestationObject']);
        //@codingStandardsIgnoreStart
        $attestationObject = CBOREncoder::decode($byteString);
        //@codingStandardsIgnoreEnd
        if (empty($attestationObject['fmt'])
            || empty($attestationObject['authData'])
        ) {
            throw new ValidationException(__('Invalid U2F key data'));
        }

        $byteString = $attestationObject['authData']->get_byte_string();

        // @see https://www.w3.org/TR/webauthn/#sec-authenticator-data
        $attestationObject['rpIdHash'] = substr($byteString, 0, 32);
        $attestationObject['flags'] = ord(substr($byteString, 32, 1));
        $attestationObject['counter'] = substr($byteString, 33, 4);

        $hashId = hash('sha256', $this->config->getDomain(), true);
        if ($hashId !== $attestationObject['rpIdHash']) {
            throw new ValidationException(__('Invalid U2F key data'));
        }

        // User presence, attestation data
        if (!($attestationObject['flags'] & 0b1000001)) {
            throw new ValidationException(__('Invalid U2F key data'));
        }

        $attestationObject['attestationData'] = [
            'aaguid' => substr($byteString, 37, 16),
            'credentialIdLength' => (ord($byteString[53]) << 8) + ord($byteString[54]),
        ];
        $attestationObject['attestationData']['credId'] = substr(
            $byteString,
            55,
            $attestationObject['attestationData']['credentialIdLength']
        );
        $cborPublicKey  = substr($byteString, 55 + $attestationObject['attestationData']['credentialIdLength']);

        $attestationObject['attestationData']['keyBytes'] = $this->COSEECDHAtoPKCS($cborPublicKey);

        if (empty($attestationObject['attestationData']['keyBytes'])
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            || $attestationObject['attestationData']['credId'] !== base64_decode($credentialData['id'])
        ) {
            throw new ValidationException(__('Invalid U2F key data'));
        }

        return [
            'key' => $attestationObject['attestationData']['keyBytes'],
            'id' => $credentialData['id'],
            'aaguid' => empty($attestationObject['attestationData']['aaguid'])
                ? null
                : base64_encode($attestationObject['attestationData']['aaguid'])
        ];
    }

    /**
     * Convert a binary string to an array of unsigned 8 bit integers
     *
     * @param string $byteString
     * @return array
     */
    private function convertBytesToArray(string $byteString): array
    {
        $result = [];
        $numberOfBytes = strlen($byteString);
        for ($i = 0; $i < $numberOfBytes; $i++) {
            $result[] = ord($byteString[$i]);
        }
        return $result;
    }

    /**
     * Convert an array of unsigned 8 bit integers into a byte string
     *
     * @param array $bytes
     * @return string
     */
    private function convertArrayToBytes(array $bytes): string
    {
        $byteString = '';

        foreach ($bytes as $byte) {
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $byteString .= chr((int)$byte);
        }

        return $byteString;
    }

    /**
     * Convert a CBOR encoded public key to PKCS format
     *
     * @param string $binary
     * @return string|null
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function COSEECDHAtoPKCS(string $binary): ?string
    {
        //@codingStandardsIgnoreStart
        $cosePubKey = CBOREncoder::decode($binary);
        //@codingStandardsIgnoreEnd

        // Sections 7.1 and 13.1.1 of @see https://tools.ietf.org/html/rfc8152
        if (!isset($cosePubKey[3])
            || $cosePubKey[3] !== self::ES256
            || !isset($cosePubKey[-1])
            || $cosePubKey[-1] != 1
            || !isset($cosePubKey[1])
            || $cosePubKey[1] != 2
            || !isset($cosePubKey[-2])
            || !isset($cosePubKey[-3])
        ) {
            return null;
        }

        $x = $cosePubKey[-2]->get_byte_string();
        $y = $cosePubKey[-3]->get_byte_string();
        if (strlen($x) != 32 || strlen($y) != 32) {
            return null;
        }

        $tag = "\x04";
        return $this->convertToPem($tag . $x . $y);
    }

    /**
     * Transform a WebAuthn public key to PEM format
     *
     * @param string $key
     * @return string|null
     * @see https://github.com/Yubico/php-u2flib-server/blob/master/src/u2flib_server/U2F.php
     */
    private function convertToPem(string $key): ?string
    {
        if (strlen($key) !== self::PUBKEY_LEN || $key[0] !== "\x04") {
            return null;
        }

        /*
         * Convert the public key to binary DER format first
         * Using the ECC SubjectPublicKeyInfo OIDs from RFC 5480
         *
         *  SEQUENCE(2 elem)                        30 59
         *   SEQUENCE(2 elem)                       30 13
         *    OID1.2.840.10045.2.1 (id-ecPublicKey) 06 07 2a 86 48 ce 3d 02 01
         *    OID1.2.840.10045.3.1.7 (secp256r1)    06 08 2a 86 48 ce 3d 03 01 07
         *   BIT STRING(520 bit)                    03 42 ..key..
         */
        $der  = "\x30\x59\x30\x13\x06\x07\x2a\x86\x48\xce\x3d\x02\x01";
        $der .= "\x06\x08\x2a\x86\x48\xce\x3d\x03\x01\x07\x03\x42";
        $der .= "\0".$key;

        $pem  = "-----BEGIN PUBLIC KEY-----\r\n";
        $pem .= chunk_split(base64_encode($der), 64);
        $pem .= "-----END PUBLIC KEY-----";

        return $pem;
    }
}
