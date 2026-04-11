# Security Policy

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 0.1.x   | :white_check_mark: |

## Reporting a Vulnerability

If you discover a security vulnerability within `crontinel/php`, please send an email to **security@crontinel.com**.

All security vulnerabilities will be promptly addressed.

Please include the following details in your report:

- A description of the vulnerability and its potential impact
- Steps to reproduce the issue (if possible)
- The version(s) affected
- Any suggested fixes (optional)

## Response Timeline

- **Acknowledgement**: Within 48 hours, you will receive confirmation that we received your report.
- **Initial Assessment**: We aim to provide a detailed assessment within 5 business days.
- **Resolution**: We work to release a fix as quickly as possible, typically within 14 days for critical issues.
- **Disclosure**: Once a fix is available, we will credit the reporter (unless anonymity is requested) in the release notes.

## Security Best Practices (for users)

When using `crontinel/php` in your application:

- **Webhook URLs**: If implementing a custom `AlertChannelInterface` with webhook delivery, ensure the target URL is kept secret and accessed only over HTTPS.
- **Cache backend**: The `AlertManager` uses your PSR-16 cache for alert deduplication. Use a secure, access-controlled cache backend (e.g., Redis with appropriate ACLs).
- **Alert content**: Alert events may contain sensitive information from your application. Ensure your log aggregation and monitoring infrastructure is secured.
- **Network access**: If your monitors perform network checks (e.g., checking disk space on remote mounts), restrict access appropriately.
