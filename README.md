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

See `.cpanel.yml` for the Git-deploy task list and the setup steps documented
at the top of that file (document root, `.env`, one-time `key:generate`).
The server never runs `npm run build` — it has no Node runtime — so compiled
assets must already be committed and up to date before deploying.

## Testing / linting

```bash
vendor/bin/pint --test   # code style
php artisan test         # PHPUnit
```
