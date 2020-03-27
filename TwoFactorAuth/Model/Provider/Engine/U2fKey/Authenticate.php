<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey;

use Magento\Framework\DataObjectFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\Integration\Model\Oauth\TokenFactory;
use Magento\TwoFactorAuth\Api\Data\U2FWebAuthnRequestInterface;
use Magento\TwoFactorAuth\Api\Data\U2FWebAuthnRequestInterfaceFactory;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\U2fKeyAuthenticateInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use Magento\TwoFactorAuth\Model\AlertInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey;
use Magento\TwoFactorAuth\Model\UserAuthenticator;

/**
 * Authenticate with the u2f provider and get an admin token
 */
class Authenticate implements U2fKeyAuthenticateInterface
{
    private const AUTHENTICATION_CHALLENGE_KEY = 'webapi_authentication_challenge';

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var U2fKey
     */
    private $u2fKey;

    /**
     * @var AlertInterface
     */
    private $alert;

    /**
     * @var TokenFactory
     */
    private $tokenFactory;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var UserAuthenticator
     */
    private $userAuthenticator;

    /**
     * @var U2FWebAuthnRequestInterfaceFactory
     */
    private $authnRequestInterfaceFactory;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var UserConfigManagerInterface
     */
    private $configManager;

    /**
     * @param TfaInterface $tfa
     * @param U2fKey $u2fKey
     * @param AlertInterface $alert
     * @param TokenFactory $tokenFactory
     * @param DataObjectFactory $dataObjectFactory
     * @param UserAuthenticator $userAuthenticator
     * @param U2FWebAuthnRequestInterfaceFactory $authnRequestInterfaceFactory
     * @param Json $json
     * @param UserConfigManagerInterface $configManager
     */
    public function __construct(
        TfaInterface $tfa,
        U2fKey $u2fKey,
        AlertInterface $alert,
        TokenFactory $tokenFactory,
        DataObjectFactory $dataObjectFactory,
        UserAuthenticator $userAuthenticator,
        U2FWebAuthnRequestInterfaceFactory $authnRequestInterfaceFactory,
        Json $json,
        UserConfigManagerInterface $configManager
    ) {
        $this->tfa = $tfa;
        $this->u2fKey = $u2fKey;
        $this->alert = $alert;
        $this->tokenFactory = $tokenFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->userAuthenticator = $userAuthenticator;
        $this->authnRequestInterfaceFactory = $authnRequestInterfaceFactory;
        $this->json = $json;
        $this->configManager = $configManager;
    }

    /**
     * @inheritDoc
     */
    public function getAuthenticationData(string $username, string $password): U2FWebAuthnRequestInterface
    {
        $user = $this->userAuthenticator->authenticateWithCredentials($username, $password);
        $userId = (int)$user->getId();

        if (!$this->tfa->getProviderIsAllowed($userId, U2fKey::CODE)) {
            throw new WebApiException(__('Provider is not allowed.'));
        } elseif (!$this->tfa->getProviderByCode(U2fKey::CODE)->isActive($userId)) {
            throw new WebApiException(__('Provider is not configured.'));
        }

        $data = $this->u2fKey->getAuthenticateData($user);
        $this->configManager->addProviderConfig(
            $userId,
            U2fKey::CODE,
            [self::AUTHENTICATION_CHALLENGE_KEY => $data['credentialRequestOptions']['challenge']]
        );

        $json = $this->json->serialize($data);

        return $this->authnRequestInterfaceFactory->create(
            [
                'data' => [
                    U2FWebAuthnRequestInterface::CREDENTIAL_REQUEST_OPTIONS_JSON => $json
                ]
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function verify(string $username, string $password, string $publicKeyCredentialJson): string
    {
        $user = $this->userAuthenticator->authenticateWithCredentials($username, $password);
        $userId = (int)$user->getId();

        $config = $this->configManager->getProviderConfig($userId, U2fKey::CODE);
        if (!$this->tfa->getProviderIsAllowed($userId, U2fKey::CODE)) {
            throw new WebApiException(__('Provider is not allowed.'));
        } elseif (!$this->tfa->getProviderByCode(U2fKey::CODE)->isActive($userId)) {
            throw new WebApiException(__('Provider is not configured.'));
        } elseif (empty($config[self::AUTHENTICATION_CHALLENGE_KEY])) {
            throw new WebApiException(__('U2f authentication prompt not sent.'));
        }

        try {
            $this->u2fKey->verify($user, $this->dataObjectFactory->create(
                [
                    'data' => [
                        'publicKeyCredential' => $this->json->unserialize($publicKeyCredentialJson),
                        'originalChallenge' => $config[self::AUTHENTICATION_CHALLENGE_KEY]
                    ]
                ]
            ));
        } catch (\Exception $e) {
            $this->alert->event(
                'Magento_TwoFactorAuth',
                'U2F error',
                AlertInterface::LEVEL_ERROR,
                $user->getUserName(),
                $e->getMessage()
            );
            throw $e;
        }

        $this->configManager->addProviderConfig(
            $userId,
            U2fKey::CODE,
            [self::AUTHENTICATION_CHALLENGE_KEY => null]
        );

        return $this->tokenFactory->create()
            ->createAdminToken($userId)
            ->getToken();
    }
}
