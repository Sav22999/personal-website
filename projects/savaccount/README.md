# SavAccount — API

The site (and the OpenID Connect issuer) is **https://www.savaccount.eu** (GitHub: Sav22999/savaccount). This folder of
saveriomorelli.com hosts what needs a database: accounts, two-step verification, services, OpenID Connect.

```
browser / service ──► savaccount.eu (gateway.php) ──X-Sav-Gateway key──► api/index.php?r=<action> ──► MySQL (sva_ tables)
```

Browsers never call this API: without the gateway key every call is a 403 (only `r=health` is open).

| Path | What |
| --- | --- |
| `api/index.php` | Router |
| `lib/routes/` | `auth` (sign-up, sign-in, reset), `account` (profile, addresses, 2FA, sessions, services, export, deletion), `oidc`, `admin` |
| `lib/` | `bootstrap` (config, database, crypto, sessions, limits), `accounts` (rules, challenges), `oidc` (provider), `totp`, `mail`, `smtp`, `schema` |
| `tests/api-test.php` | End-to-end test, only on a database whose name ends with `_test` |
| `credentials.php` | Git-ignored; template in `credentials-sample.php` |

## Going live

1. Fill in `smtp_password` (and check `$master_emails`) in `credentials.php`.
2. Back up `$secret_key` somewhere safe: without it no password works any more.
3. Set `$enabled = true` and upload. The tables are created by the first request.
4. Register on savaccount.eu with a master address, turn on two-step verification, create the services.

## Security choices

- Passwords: Argon2id over an HMAC with a key outside the database; minimum 10 characters; leaked passwords refused.
- Sessions, links, codes, OAuth tokens and client secrets are stored only as hashes. Authenticator secrets and the RSA
  signing key are encrypted (AES-256-GCM).
- Codes: 6 digits, 15 minutes, 5 attempts, counted before they are compared. Links do nothing until confirmed, and
  confirming a new account with its link asks for its password.
- The answers do not tell whether an address has an account.
- Sensitive actions need the password (and the second step) again in the last 10 minutes.
- OpenID Connect: code flow only, PKCE S256 for everyone, exact redirect addresses, rotating refresh tokens with reuse
  detection, pairwise subject identifiers by default, consent always asked.
- Administrators must have two-step verification on.
- Kept data: security log 90 days with a shortened IP, rate limits 24 hours as keyed hashes, unconfirmed accounts 48 hours.

Rotating the signing key: set `retired_at = UTC_TIMESTAMP()` on the row in `sva_signing_keys`; a new one is made at the next sign-in.
