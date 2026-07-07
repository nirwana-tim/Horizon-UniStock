# Ward Security Report

**Project:** laravel/react-starter-kit  
**Laravel:** ^13.0  
**PHP:** ^8.3  
**Duration:** 8.165s  
**Scanners:** env-scanner, config-scanner, dependency-scanner  

## Summary

| Total | 27 |
|-------|---|
| 🔴 Critical | 2 |
| 🟠 High | 6 |
| 🟡 Medium | 14 |
| 🟢 Low | 5 |

## Findings

### 🔴 Critical (2)

#### CVE-2026-45034 — [CVE-2026-45034] phpoffice/phpspreadsheet@1.30.0 — PHPSpreadsheet has a patch bypass for CVE-2026-34084 

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

PHPSpreadsheet has a patch bypass for CVE-2026-34084 

**Remediation:**

Upgrade phpoffice/phpspreadsheet to 1.30.5 or later:
  composer require phpoffice/phpspreadsheet:1.30.5

**References:**
- https://github.com/PHPOffice/PhpSpreadsheet/security/advisories/GHSA-87m4-826x-3crx
- https://github.com/advisories/GHSA-q4q6-r8wh-5cgh

---

#### CVE-2026-34084 — [CVE-2026-34084] phpoffice/phpspreadsheet@1.30.0 — PhpSpreadsheet has SSRF/RCE in IOFactory::load when $filename is user controlled

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

PhpSpreadsheet has SSRF/RCE in IOFactory::load when $filename is user controlled

**Remediation:**

Upgrade phpoffice/phpspreadsheet to 5.6.0 or later:
  composer require phpoffice/phpspreadsheet:5.6.0

**References:**
- https://github.com/PHPOffice/PhpSpreadsheet/security/advisories/GHSA-q4q6-r8wh-5cgh
- https://nvd.nist.gov/vuln/detail/CVE-2026-34084
- https://www.php.net/manual/en/wrappers.php

---

### 🟠 High (6)

#### ENV-002 — APP_DEBUG is enabled

- **File:** `.env:4`
- **Category:** Configuration
- **Scanner:** env-scanner

APP_DEBUG is set to true. In production, this exposes detailed error messages including stack traces, database queries, and environment variables to end users.

```
APP_DEBUG=true
```

**Remediation:**

Set APP_DEBUG=false in your production .env file. Use Laravel's logging system for error tracking instead.

**References:**
- https://owasp.org/Top10/A05_2021-Security_Misconfiguration/

---

#### CVE-2026-40902 — [CVE-2026-40902] phpoffice/phpspreadsheet@1.30.0 — PhpSpreadsheet has CPU Denial of Service via Unbounded Row Number in XLSX Row Dimensions

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

PhpSpreadsheet has CPU Denial of Service via Unbounded Row Number in XLSX Row Dimensions

**Remediation:**

Upgrade phpoffice/phpspreadsheet to 5.7.0 or later:
  composer require phpoffice/phpspreadsheet:5.7.0

**References:**
- https://github.com/PHPOffice/PhpSpreadsheet/security/advisories/GHSA-7c6m-4442-2x6m
- https://nvd.nist.gov/vuln/detail/CVE-2026-40902

---

#### CVE-2026-40863 — [CVE-2026-40863] phpoffice/phpspreadsheet@1.30.0 — PhpSpreadsheet has CPU Denial of Service via Unbounded Row Index in SpreadsheetML XML Reader

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

PhpSpreadsheet has CPU Denial of Service via Unbounded Row Index in SpreadsheetML XML Reader

**Remediation:**

Upgrade phpoffice/phpspreadsheet to 5.7.0 or later:
  composer require phpoffice/phpspreadsheet:5.7.0

**References:**
- https://github.com/PHPOffice/PhpSpreadsheet/security/advisories/GHSA-84wq-86v6-x5j6
- https://nvd.nist.gov/vuln/detail/CVE-2026-40863

---

#### CVE-2026-45075 — [CVE-2026-45075] symfony/http-kernel@8.0.8 — Symfony's HEAD Request Bypasses methods: ['GET'] Filter in #[IsGranted] / #[IsSignatureValid] / #[IsCsrfTokenValid]

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

Symfony's HEAD Request Bypasses methods: ['GET'] Filter in #[IsGranted] / #[IsSignatureValid] / #[IsCsrfTokenValid]

**Remediation:**

Upgrade symfony/http-kernel to 7.4.12 or later:
  composer require symfony/http-kernel:7.4.12

**References:**
- https://github.com/symfony/symfony/security/advisories/GHSA-6439-2f28-8p8q
- https://github.com/symfony/symfony/commit/fa8d5c67aa4b22c9656e3fd7d5c3aa59865bf838
- https://github.com/FriendsOfPHP/security-advisories/blob/master/symfony/http-kernel/CVE-2026-45075.yaml

---

#### CVE-2026-45067 — [CVE-2026-45067] symfony/mime@8.0.8 — Symfony has Email Header / SMTP Command Injection via CRLF in Symfony\Component\Mime\Address

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

Symfony has Email Header / SMTP Command Injection via CRLF in Symfony\Component\Mime\Address

**Remediation:**

Upgrade symfony/mime to 5.4.52 or later:
  composer require symfony/mime:5.4.52

**References:**
- https://github.com/symfony/symfony/security/advisories/GHSA-qpmx-3rfj-7rhv
- https://github.com/symfony/symfony/commit/dc2dbd29211eb4ddc451373fa1374fb926e94604
- https://github.com/FriendsOfPHP/security-advisories/blob/master/symfony/mime/CVE-2026-45067.yaml

---

#### GHSA-5vg9-5847-vvmq — [GHSA-5vg9-5847-vvmq] laravel/framework@13.2.0 — Laravel Framework: CRLF injection in default email rule 

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

Laravel Framework: CRLF injection in default email rule 

**Remediation:**

Upgrade laravel/framework to 13.10.0 or later:
  composer require laravel/framework:13.10.0

**References:**
- https://github.com/laravel/framework/security/advisories/GHSA-5vg9-5847-vvmq

---

### 🟡 Medium (14)

#### ENV-005 — APP_ENV is set to 'local'

- **File:** `.env:2`
- **Category:** Configuration
- **Scanner:** env-scanner

The application environment suggests a non-production configuration. If this is a production server, this may cause debug features to be enabled and performance optimizations to be skipped.

```
APP_ENV=local
```

**Remediation:**

Set APP_ENV=production on production servers.

---

#### CVE-2026-35453 — [CVE-2026-35453] phpoffice/phpspreadsheet@1.30.0 — PhpSpreadsheet has XSS via NumberFormat @ Text Substitution in HTML Writer

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

PhpSpreadsheet has XSS via NumberFormat @ Text Substitution in HTML Writer

**Remediation:**

Upgrade phpoffice/phpspreadsheet to 5.7.0 or later:
  composer require phpoffice/phpspreadsheet:5.7.0

**References:**
- https://github.com/PHPOffice/PhpSpreadsheet/security/advisories/GHSA-6wpp-88cp-7q68
- https://nvd.nist.gov/vuln/detail/CVE-2026-35453

---

#### CVE-2026-40296 — [CVE-2026-40296] phpoffice/phpspreadsheet@1.30.0 — PhpSpreadsheet has XSS via number format code with @ text placeholder bypasses htmlspecialchars in HTML writer

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

PhpSpreadsheet has XSS via number format code with @ text placeholder bypasses htmlspecialchars in HTML writer

**Remediation:**

Upgrade phpoffice/phpspreadsheet to 5.7.0 or later:
  composer require phpoffice/phpspreadsheet:5.7.0

**References:**
- https://github.com/PHPOffice/PhpSpreadsheet/security/advisories/GHSA-hrmw-qprp-wgmc
- https://nvd.nist.gov/vuln/detail/CVE-2026-40296

---

#### CVE-2026-48736 — [CVE-2026-48736] symfony/http-foundation@8.0.8 — Symfony: IpUtils::PRIVATE_SUBNETS Omits IPv6 Transition Forms (6to4, NAT64, Teredo, IPv4-compatible): SSRF Bypass in NoPrivateNetworkHttpClient

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

Symfony: IpUtils::PRIVATE_SUBNETS Omits IPv6 Transition Forms (6to4, NAT64, Teredo, IPv4-compatible): SSRF Bypass in NoPrivateNetworkHttpClient

**Remediation:**

Upgrade symfony/http-foundation to 6.4.41 or later:
  composer require symfony/http-foundation:6.4.41

**References:**
- https://github.com/symfony/symfony/security/advisories/GHSA-38cx-cq6f-5755
- https://github.com/symfony/symfony/commit/82765368cf74177c36613575182f168a2eb765b2
- https://github.com/symfony/symfony/commit/85b831555be8ea1f43bf01078afe87bc4c92f65e

---

#### CVE-2026-45070 — [CVE-2026-45070] symfony/mime@8.0.8 — Symfony has Email Header Injection via Non-Token Characters in Mime Parameter Names

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

Symfony has Email Header Injection via Non-Token Characters in Mime Parameter Names

**Remediation:**

Upgrade symfony/mime to 5.4.52 or later:
  composer require symfony/mime:5.4.52

**References:**
- https://github.com/symfony/symfony/security/advisories/GHSA-vqc8-7275-q272
- https://github.com/FriendsOfPHP/security-advisories/blob/master/symfony/mime/CVE-2026-45070.yaml
- https://github.com/FriendsOfPHP/security-advisories/blob/master/symfony/symfony/CVE-2026-45070.yaml

---

#### CVE-2026-45068 — [CVE-2026-45068] symfony/mailer@8.0.8 — Symfony has an Argument Injection in SendmailTransport via Dash-Prefixed Recipient Address

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

Symfony has an Argument Injection in SendmailTransport via Dash-Prefixed Recipient Address

**Remediation:**

Upgrade symfony/mailer to 5.4.52 or later:
  composer require symfony/mailer:5.4.52

**References:**
- https://github.com/symfony/symfony/security/advisories/GHSA-xx3c-qf5g-hc39
- https://github.com/symfony/symfony/commit/c45144862dc289d03952f41f6078174089a3afc6
- https://github.com/FriendsOfPHP/security-advisories/blob/master/symfony/mailer/CVE-2026-45068.yaml

---

#### CVE-2026-45065 — [CVE-2026-45065] symfony/routing@8.0.8 — Symfony has a UrlGenerator Route-Requirement Bypass via Unanchored Regex Alternation → Off-Site //host URL Injection

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

Symfony has a UrlGenerator Route-Requirement Bypass via Unanchored Regex Alternation → Off-Site //host URL Injection

**Remediation:**

Upgrade symfony/routing to 5.4.52 or later:
  composer require symfony/routing:5.4.52

**References:**
- https://github.com/symfony/symfony/security/advisories/GHSA-72xp-p242-47p9
- https://github.com/symfony/symfony/commit/bcf487c22f3240ba994124e0e0fe8616f3cfc47a
- https://github.com/FriendsOfPHP/security-advisories/blob/master/symfony/routing/CVE-2026-45065.yaml

---

#### CVE-2026-48784 — [CVE-2026-48784] symfony/routing@8.0.8 — Symfony: UrlGenerator Dot-Segment Encoding Skips Every Other Chained `../` or `./` → Generated URL Collapses Off-Route Under RFC 3986 Normalization

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

Symfony: UrlGenerator Dot-Segment Encoding Skips Every Other Chained `../` or `./` → Generated URL Collapses Off-Route Under RFC 3986 Normalization

**Remediation:**

Upgrade symfony/routing to 5.4.53 or later:
  composer require symfony/routing:5.4.53

**References:**
- https://github.com/symfony/symfony/security/advisories/GHSA-h5x3-xfc9-m39h
- https://github.com/symfony/symfony/commit/4b63c3a3f7af04ecd79c89a594b0b02a01990b1d
- https://github.com/FriendsOfPHP/security-advisories/blob/master/symfony/routing/CVE-2026-48784.yaml

---

#### CVE-2026-48998 — [CVE-2026-48998] guzzlehttp/psr7@2.9.0 — guzzlehttp/psr7 has Host Confusion via Authority Reinterpretation

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

guzzlehttp/psr7 has Host Confusion via Authority Reinterpretation

**Remediation:**

Upgrade guzzlehttp/psr7 to 2.10.2 or later:
  composer require guzzlehttp/psr7:2.10.2

**References:**
- https://github.com/guzzle/psr7/security/advisories/GHSA-34xg-wgjx-8xph
- https://nvd.nist.gov/vuln/detail/CVE-2026-48998

---

#### CVE-2026-49214 — [CVE-2026-49214] guzzlehttp/psr7@2.9.0 — guzzlehttp/psr7 has CRLF Injection via URI Host Component

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

guzzlehttp/psr7 has CRLF Injection via URI Host Component

**Remediation:**

Upgrade guzzlehttp/psr7 to 2.10.2 or later:
  composer require guzzlehttp/psr7:2.10.2

**References:**
- https://github.com/guzzle/psr7/security/advisories/GHSA-hq7v-mx3g-29hw
- https://nvd.nist.gov/vuln/detail/CVE-2026-49214

---

#### CVE-2026-55766 — [CVE-2026-55766] guzzlehttp/psr7@2.9.0 — guzzlehttp/psr7: CRLF Injection in HTTP Start-Line Serialization

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

guzzlehttp/psr7: CRLF Injection in HTTP Start-Line Serialization

**Remediation:**

Upgrade guzzlehttp/psr7 to 2.12.1 or later:
  composer require guzzlehttp/psr7:2.12.1

**References:**
- https://github.com/guzzle/psr7/security/advisories/GHSA-vm85-hxw5-5432

---

#### CVE-2026-55767 — [CVE-2026-55767] guzzlehttp/guzzle@7.10.0 — guzzlehttp/guzzle: Dot-Only Cookie Domains Match All Hosts

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

guzzlehttp/guzzle: Dot-Only Cookie Domains Match All Hosts

**Remediation:**

Upgrade guzzlehttp/guzzle to 7.12.1 or later:
  composer require guzzlehttp/guzzle:7.12.1

**References:**
- https://github.com/guzzle/guzzle/security/advisories/GHSA-cwxw-98qj-8qjx

---

#### CVE-2026-55568 — [CVE-2026-55568] guzzlehttp/guzzle@7.10.0 — guzzlehttp/guzzle: Silent HTTPS-Proxy Downgrade to Cleartext

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

guzzlehttp/guzzle: Silent HTTPS-Proxy Downgrade to Cleartext

**Remediation:**

Upgrade guzzlehttp/guzzle to 7.12.1 or later:
  composer require guzzlehttp/guzzle:7.12.1

**References:**
- https://github.com/guzzle/guzzle/security/advisories/GHSA-wpwq-4j6v-78m3

---

#### GHSA-crmm-hgp2-wgrp — [GHSA-crmm-hgp2-wgrp] laravel/framework@13.2.0 — Laravel Framework: Temporary Signed URL Path Confusion

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

Laravel Framework: Temporary Signed URL Path Confusion

**Remediation:**

Upgrade laravel/framework to 13.12.0 or later:
  composer require laravel/framework:13.12.0

**References:**
- https://github.com/laravel/framework/security/advisories/GHSA-crmm-hgp2-wgrp
- https://github.com/laravel/framework/pull/60137
- https://github.com/laravel/framework/pull/60230

---

### 🟢 Low (5)

#### ENV-006 — Database password is empty

- **File:** `.env:28`
- **Category:** Configuration
- **Scanner:** env-scanner

DB_PASSWORD is set to an empty string. While this may be valid for local development with trust authentication, it's a security risk if this configuration reaches production.

```
DB_PASSWORD=
```

**Remediation:**

Set a strong database password for non-local environments.

---

#### CVE-2026-46644 — [CVE-2026-46644] symfony/polyfill-intl-idn@1.33.0 — symfony/polyfill-intl-idn: xn-- labels with ASCII-only Punycode payloads are treated as equivalent to their decoded form

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

symfony/polyfill-intl-idn: xn-- labels with ASCII-only Punycode payloads are treated as equivalent to their decoded form

**Remediation:**

Upgrade symfony/polyfill-intl-idn to 1.38.1 or later:
  composer require symfony/polyfill-intl-idn:1.38.1

**References:**
- https://github.com/symfony/polyfill/security/advisories/GHSA-2xf4-cg6j-vhgq
- https://github.com/symfony/polyfill/commit/1be936e2491ccebe152bd736dfc91eb1422c8bec
- https://github.com/FriendsOfPHP/security-advisories/blob/master/symfony/polyfill-intl-idn/CVE-2026-46644.yaml

---

#### CVE-2026-45304 — [CVE-2026-45304] symfony/yaml@8.0.8 — Symfony's YAML Parser Vulnerable to Exponential Memory Allocation via Recursive Collection-Alias Expansion ("Billion Laughs")

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

Symfony's YAML Parser Vulnerable to Exponential Memory Allocation via Recursive Collection-Alias Expansion ("Billion Laughs")

**Remediation:**

Upgrade symfony/yaml to 5.4.52 or later:
  composer require symfony/yaml:5.4.52

**References:**
- https://github.com/symfony/symfony/security/advisories/GHSA-4qpc-3hr4-r2p4
- https://github.com/symfony/symfony/commit/e77391b2e4f18821198f010d573674c8ed4a970a
- https://github.com/FriendsOfPHP/security-advisories/blob/master/symfony/symfony/CVE-2026-45304.yaml

---

#### CVE-2026-45305 — [CVE-2026-45305] symfony/yaml@8.0.8 — Symfony's YAML Parser has a ReDoS via Catastrophic Backtracking in Parser::cleanup() Regex

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

Symfony's YAML Parser has a ReDoS via Catastrophic Backtracking in Parser::cleanup() Regex

**Remediation:**

Upgrade symfony/yaml to 5.4.52 or later:
  composer require symfony/yaml:5.4.52

**References:**
- https://github.com/symfony/symfony/security/advisories/GHSA-9frc-8383-795m
- https://github.com/symfony/symfony/commit/9749cd43c5e09b3735093623670b21b9d8a056cb
- https://github.com/FriendsOfPHP/security-advisories/blob/master/symfony/symfony/CVE-2026-45305.yaml

---

#### CVE-2026-45133 — [CVE-2026-45133] symfony/yaml@8.0.8 — Symfony hardened the parser when handling untrusted input

- **File:** `composer.lock:0`
- **Category:** Dependencies
- **Scanner:** dependency-scanner

Symfony hardened the parser when handling untrusted input

**Remediation:**

Upgrade symfony/yaml to 5.4.52 or later:
  composer require symfony/yaml:5.4.52

**References:**
- https://github.com/symfony/symfony/security/advisories/GHSA-c2p3-7m5p-cv8x
- https://github.com/symfony/symfony/commit/914f427ed9630ddb3904dafba763e53d9f133fe3
- https://github.com/FriendsOfPHP/security-advisories/blob/master/symfony/symfony/CVE-2026-45133.yaml

---

*Generated by [Ward](https://github.com/Eljakani/ward) v0.4.2*
