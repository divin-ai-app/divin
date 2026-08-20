# divin.ai

An open, AI-engine-agnostic business registry. See [docs/divin_ai_claude_code_prompt.md](docs/divin_ai_claude_code_prompt.md)
for the full product brief.

## Stack

- **Laravel 13** (PHP 8.3) — server-rendered Blade views, no SPA/client-only
  shell, matching the product's own "be crawlable" thesis.
- **MySQL/MariaDB** via Eloquent — the target HostGator cPanel account is
  shared/PHP-only (no Node.js App support, no root), so the stack is plain
  LAMP: Apache + PHP + MySQL, deployed via cPanel's Git Version Control.
- **Tailwind CSS v4** (CSS-first `@theme` tokens in `resources/css/app.css`)
  + **Vite**, used as a **build-time-only** tool — the production server has
  no Node runtime, so compiled assets in `public/build` are committed to git
  (see `.gitignore`) rather than built on deploy. Run `npm run build` locally
  before pushing any change under `resources/css` or `resources/js`.
- **Resend** for transactional email (magic-link login, claim OTP, freshness
  alerts) in production; local dev uses Laragon's Mailpit SMTP catcher
  (`http://localhost:8025`).
- **Stripe** for annual-billing subscriptions (scaffolded, not fully wired).

## Local development (Windows + Laragon)

```bash
composer install
npm install
cp .env.example .env   # then fill in local DB_* values (see below)
php artisan key:generate
php artisan migrate
php artisan storage:link
npm run build           # or `npm run dev` while iterating on styles/JS
php artisan serve
```

Local `.env` expects Laragon running (`Start All` in the Laragon app) with a
`divinai` MySQL database and Mailpit for mail — see the committed `.env` for
the exact local values already in use during development.

## Deployment (cPanel)

**Live at https://divin.ai**, deployed to a real HostGator shared/cPanel
account (no root, no SSH key auth needed — cPanel's Terminal app + Git
Version Control is enough). See `.cpanel.yml` for the Git-deploy task list
and the setup steps documented at the top of that file.

Confirmed quirks of this specific hosting environment, already handled by
`.cpanel.yml` and worth knowing if something needs manual poking:

- **No Composer pre-installed** — bare `composer` isn't on `$PATH` in the
  jailed shell. `.cpanel.yml` installs `~/composer.phar` itself if missing
  and calls `php ~/composer.phar` everywhere.
- **cPanel Git Version Control clones default to `700` permissions** on the
  repo folder and its parent, which silently 403s the whole site (Apache
  can't traverse in) even though `public/` itself is fine. `.cpanel.yml`
  runs `chmod 711` on both every deploy.
- **The server never runs `npm run build`** — it has no Node runtime — so
  compiled Vite/Tailwind assets in `public/build` must already be committed
  and up to date before pushing/deploying.
- **Resend requires domain verification before it will send** from an
  `@divin.ai` address — done once via Resend's Domains page (adds a DKIM
  `TXT` and an SPF `MX`+`TXT` pair under the `send.` subdomain in cPanel's
  Zone Editor). Already verified for this deployment; if the domain or
  Resend account ever changes, redo that first or mail silently fails with
  a `TransportException` and no delivery, even though the app itself
  reports success.
- Mail hosting for `hello@divin.ai` (real inboxes, e.g. Roundcube) is
  separate from Resend and untouched by any of the above — Resend is
  send-only (`Enable Receiving` stays off in its dashboard).

## Testing / linting

```bash
vendor/bin/pint --test   # code style
php artisan test         # PHPUnit
```
