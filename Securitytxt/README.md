# Security.txt

### Summary
> When security risks in web services are discovered by independent security researchers who understand the severity of the risk, they often lack the channels to properly disclose them. As a result, security issues may be left unreported. Security.txt defines a standard to help organizations define the process for security researchers to securely disclose security vulnerabilities.

Source: https://tools.ietf.org/html/draft-foudil-securitytxt-01

The Magento_Securitytxt module provides the following functionality: 
* allows to save the security configurations in the admin panel
* contains a router to match application action class for requests to the `.well-known/security.txt` and `.well-known/security.txt.sig` files.
* serves the content of the `.well-known/security.txt` and `.well-known/security.txt.sig` files.

A valid security.txt file could look like the following example:

```
Contact: security@example.com
Encryption: https://example.com/pgp.asc
Acknowledgement: https://example.com/security/hall-of-fame
Policy: https://example.com/security-policy.html
Signature: https://example.com/.well-known/security.txt.sig
```
Security.txt can be accessed at below location:
`https://example.com/.well-known/security.txt`

To create security.txt signature (security.txt.sig) file:

`gpg -u KEYID --output security.txt.sig --armor --detach-sig security.txt`

To verify the security.txt file's signature:

`gpg --verify security.txt.sig security.txt`