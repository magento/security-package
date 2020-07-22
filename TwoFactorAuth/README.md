MSP TwoFactorAuth

Two Factor Authentication module for maximum **backend access protection** in Magento 2.

> Member of **MSP Security Suite**
>
> See: https://github.com/magespecialist/m2-MSP_Security_Suite

Did you lock yourself out from Magento backend? <a href="https://github.com/magespecialist/m2-Magento_TwoFactorAuth#emergency-commandline-disable">click here.</a>

## Main features:

* Providers:
    * Google authenticator
        * QR code enroll
    * Authy
        * SMS
        * Call
        * Token
        * One touch
    * U2F keys (Yubico and others)
    * Duo Security
        * SMS
        * Push notification
* Central security suite events logging
* Per user configuration
* Forced global 2FA configuration

## Installing on Magento2:

**1. Install using composer**

From command line: 

`composer require msp/twofactorauth`

**2. Enable and configure from your Magento backend config**

Enable from **Store > Config > SecuritySuite > Two Factor Authentication**.

<img src="https://raw.githubusercontent.com/magespecialist/m2-Magento_TwoFactorAuth/master/screenshots/config.png" />

**3. Enable two factor authentication for your user**

You can select among a set of different 2FA providers. **Multiple concurrent providers** are supported.

<img src="https://raw.githubusercontent.com/magespecialist/m2-Magento_TwoFactorAuth/master/screenshots/user_tfa.png" />

**4. Subscribe / Configure your 2FA provider(s):**

**4.1 Google Authenticator example**

<img src="https://raw.githubusercontent.com/magespecialist/m2-Magento_TwoFactorAuth/master/screenshots/google_qr.png" />

**4.2. Duo Security example**

<img src="https://raw.githubusercontent.com/magespecialist/m2-Magento_TwoFactorAuth/master/screenshots/duo_auth.png" />

**4.3. U2F key (Yubico and others) example**

<img src="https://raw.githubusercontent.com/magespecialist/m2-Magento_TwoFactorAuth/master/screenshots/u2f_auth.png" />

**4.4. Authy example**

<img src="https://raw.githubusercontent.com/magespecialist/m2-Magento_TwoFactorAuth/master/screenshots/authy_auth.png" />

## Emergency commandline disable:

If you messed up with two factor authentication you can disable it from command-line:

`php bin/magento msp:security:tfa:disable`

This will disable two factor auth globally.

## Emergency commandline reset:

If you need to manually reset one single user configuration (so you can restart configuration / subscription), type:
 
`php bin/magento msp:security:tfa:reset <username> <provider>`

e.g.:

`php bin/magento msp:security:tfa:reset admin google`

`php bin/magento msp:security:tfa:reset admin u2fkey`

`php bin/magento msp:security:tfa:reset admin authy`

## Emergency of emergency and your house is on fire, your dog is lost and your wife doesn't love you anymore:

**DO NOT ATTEMPT TO MODIFY ANY DB INFORMATION UNLESS YOU UNDERSTAND WHAT YOU ARE DOING**

Table `core_config_data`:
* `msp/twofactorauth/enabled`: Set to zero to disable 2fa globally
* `msp/twofactorauth/force_providers`: Delete this entry to remove forced providers option

Table `msp_tfa_user_config`:
* Delete one user row to reset user's 2FA preference and configuration

