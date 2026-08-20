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
- **cPanel's PHP version manager periodically touches `public/.htaccess`**,
  appending a `# php -- BEGIN cPanel-generated handler...` comment block and
  leaving timestamped `.htaccess.phpupgrader.*` backup files alongside it.
  Left unhandled, this makes Git Version Control refuse the *next* deploy
  ("uncommitted changes exist") since the tracked file no longer matches
  what's on disk. Fixed by committing that same comment block permanently
  into the tracked `public/.htaccess` and gitignoring the backup-file
  pattern — but if cPanel changes the PHP version/handler again and deploy
  refuses with the same error, `cd ~/repositories/divin-ai && git status`
  to see what changed, merge it into `public/.htaccess` locally the same
  way, and push.

### Cron Jobs (Phase 6 — Managed freshness + crawler visit rollup)

This hosting plan has no persistent process/scheduler daemon, so Laravel's
task scheduler (`routes/console.php`) only does anything if something
actually calls `php artisan schedule:run` periodically — nothing does that
here. Instead, point cPanel's own **Cron Jobs** app directly at each
command. In cPanel: **Advanced → Cron Jobs → Add New Cron Job**, common
settings ("Once Per Day", midnight) for both:

```bash
php /home1/dwivvkte/repositories/divin-ai/artisan freshness:check
php /home1/dwivvkte/repositories/divin-ai/artisan crawler:rollup
```

(Use the full path to whichever `php` binary cPanel's MultiPHP Manager has
selected for this domain if the bare `php` above isn't on the cron shell's
`$PATH` — check with `which php` in cPanel's Terminal app first.)

- `freshness:check` — diffs every Managed profile's DataSource rows against
  the live profile and emails owners once per new drift.
- `crawler:rollup` — rolls yesterday-and-older raw `crawler_visit_logs`
  (written on every real AI-bot hit to a public profile page, see
  `App\Http\Middleware\LogCrawlerVisit`) into `crawler_visit_daily_aggs`
  and prunes them — the dashboard's crawler-activity chart reads the
  aggregate table only, so visits never show up there until this has run.

### Backup / restore runbook (cPanel's native Backup Wizard)

What actually needs backing up here, and why: application **code** lives in
GitHub (`https://github.com/divin-ai-app/divin`) and is trivially
recoverable by re-cloning — it's not the risk. The two things that only
exist on the server are the **MySQL database** (every business profile,
claim, subscription, dispute, freshness log — everything) and **uploaded
owner images** (`storage/app/public/profile-images`, symlinked to
`public/storage`). Those are what this runbook protects.

**Taking a backup** — cPanel → **Backup Wizard** (or **Backup**) → **Backup**:

1. Choose **Full Backup** (safest, includes the database + all files +
   email) if disk space/download time allows, or at minimum a **Partial
   Backup** of the **MySQL database** plus a **Home Directory** backup — the
   two together cover both data risks above.
2. Destination: **Home Directory** is fine for cPanel to generate the
   archive, but then **download it locally** (or to cloud storage) —
   leaving the only copy on the same server it's backing up defeats the
   point if that account is ever lost entirely.
3. Do this **before every deploy that includes a migration**, and on a
   regular cadence otherwise (monthly is a reasonable floor for a
   low-write-volume registry like this one; tighten it once real customer
   data volume grows).

**Restoring:**

- *Whole-account disaster recovery* (lost the account entirely): cPanel →
  **Backup Wizard** → **Restore**, upload the full-backup archive. This
  restores files, databases, and email together.
- *Database-only rollback* (e.g. a bad migration or bad data change, files
  are fine): phpMyAdmin → **Import** the `.sql` dump from inside the backup
  archive into the existing database — faster and safer than a full restore
  when only the data is wrong, since it doesn't touch the working
  `~/repositories/divin-ai` checkout or its `.env`.
- *Code rollback* (a bad deploy, data is fine): this is a git operation,
  not a backup-wizard one — `cd ~/repositories/divin-ai && git log` to find
  the last-good commit, `git reset --hard <sha>`, then re-run the deploy
  tasks (`.cpanel.yml`'s steps) by hand if cPanel's own "Deploy HEAD
  Commit" won't target an arbitrary older commit directly.
- After any restore that touches the database, re-check
  `storage/framework/` cache state is consistent: `php artisan config:clear
  && php artisan cache:clear` from Terminal.

**Not covered by cPanel backups** — the `.env` file's secrets (`APP_KEY`,
Stripe/Resend keys, DB credentials) are gitignored and cPanel-account-local;
losing the account loses them too unless they're separately recorded
somewhere (a password manager, not this repo). Worth doing once, outside
of any automated flow.

## Testing / linting

```bash
vendor/bin/pint --test   # code style
php artisan test         # PHPUnit
```
