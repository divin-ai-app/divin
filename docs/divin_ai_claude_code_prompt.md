# Claude Code — Plan Mode Prompt: divin.ai Web Application

> Paste everything below into Claude Code in **Plan Mode**. Do not write implementation
> code yet — produce a concrete, reviewable plan first (see "What I want out of this
> session" at the end).

---

## 1. What divin.ai is

divin.ai is an **open, AI-engine-agnostic business registry**. Search behaviour is
shifting from Google to conversational AI (ChatGPT, Claude, Gemini, Grok, Perplexity).
Most small businesses — especially in Mauritius, our launch market — have no
independently owned, crawlable website: their only public presence is a Facebook Page
(which blocks most AI crawlers) and/or an OTA listing (Booking.com, Agoda, TripAdvisor),
which shows only a thin, generic summary. divin.ai fixes this by:

1. **Auto-generating** a structured, verified business profile from public data
   (business registers, Google-Business-Profile-adjacent data, OTA listings) — public,
   non-sensitive facts only (name, category, address, hours, description). No personal
   or sensitive data is ever collected or published.
2. **Publishing** that profile as a clean, schema.org-marked, server-rendered page at
   `divin.ai/{canonical-id}-{business-name}`, openly crawlable by every AI engine's bot
   (GPTBot, OAI-SearchBot, ClaudeBot, PerplexityBot, Google-Extended, etc.) — the
   registry's `robots.txt` should allow all of these by default; there is no walled
   garden here, that openness is the entire point of the business.
3. **Inviting the business owner to claim** the profile (ownership verification), then
   correct and enrich it.
4. Offering two subscription tiers, **billed annually only** (to avoid card-fee drag on
   small transactions):
   - **Registered — US$1.99/mo equivalent**: claim, verify, and enrich the profile.
   - **Managed — US$4.99/mo equivalent**: adds ongoing automated monitoring —
     freshness checks and cross-source coherence checks against the business's own
     website, Facebook Page, and OTA listings, with email alerts when sources drift out
     of agreement.
5. Showing paying customers a dashboard of **AI crawler visit activity** — explicitly
   labeled as a *consideration/visibility signal*, never as proof of citation (bot
   visits ≠ citation; do not let any UI copy imply a causal guarantee).

**Go-to-market**: Mauritius first (hotels, restaurants, clinics, real estate agencies —
businesses currently on Facebook/OTA only), then Indian Ocean, then Africa, then Asia
and the rest of the world. GDPR-zone countries (EU, UK, Réunion, Mayotte) are excluded
from auto-generation until a per-country legal review is complete — the product needs a
visible **country clearance status** concept because of this.

**Footer industry focus** (mirror this vertical structure, it's a deliberate echo of
how yext.com organizes its footer): **Healthcare, Hospitality, Retail, Food, Financial
Services** — each should eventually get its own industry landing page explaining how
divin.ai applies to that vertical specifically (e.g., Hospitality → hotel/guesthouse
AI-discoverability; Healthcare → clinic accuracy and liability-risk framing).

---

## 2. Look & feel

Design direction: **clean, confident, enterprise-SaaS — take stylistic inspiration from
yext.com's layout patterns and information architecture, but do not copy Yext's logo,
brand assets, exact copy, or proprietary imagery.** Specifically borrow the *pattern*,
not the *brand*:

- Bold, oversized hero headline + short subhead + single primary CTA above the fold.
- Confident, restrained color palette — a dark navy/near-black as primary, one accent
  color (avoid literally cloning Yext's specific blues), generous white space.
- Icon-led feature grids (3–4 columns) rather than dense paragraphs.
- A **mega-menu footer** organized by: Industries (Healthcare, Hospitality, Retail,
  Food, Financial Services), Product, Resources, Company — exactly the structural
  pattern yext.com uses.
- Logo/trust strip placeholder (space for future customer logos, empty/placeholder for
  now).
- Sticky top nav, mobile-first responsive, generous line-height, sans-serif system font
  stack unless you have a strong reason to load a webfont.

Since divin.ai's entire pitch is "we make you crawlable and well-structured for AI
engines," **the divin.ai marketing site itself must practice what it preaches**:
server-rendered (not client-only rendering that would leave AI crawlers seeing an empty
shell), full schema.org markup (Organization, Product, FAQPage where relevant), a
genuinely useful `llms.txt`, and a `robots.txt` that explicitly allows every major AI
crawler. Treat this as a real, testable requirement, not decoration.

---

## 3. Site & app map

### A. Public marketing site
- **Home** — hero, problem framing (AI search is replacing Google search; most
  businesses are invisible to it), how divin.ai works (3-step: auto-generate → claim →
  monitor), pricing teaser, industries teaser, footer mega-menu.
- **How it works** — deeper explanation of the auto-generate/claim/monitor mechanic,
  and an honest explainer of what "AI crawler visit" does and does not mean.
- **Free AI Visibility Check** — a lead-gen interactive tool: visitor enters a business
  name/location, the app shows (or fetches/mocks, per backend availability) what is
  currently and *not* currently known about that business publicly, ending in a CTA to
  claim/create a profile. This is the single highest-leverage conversion tool on the
  site — design it prominently.
- **Industries hub** + 5 individual pages: **Healthcare, Hospitality, Retail, Food,
  Financial Services** — each with vertical-specific pain points and a relevant
  example.
- **Pricing** — Registered vs. Managed comparison table, annual billing called out
  explicitly, FAQ section addressing "why annual only."
- **About** — company story, Mauritius-first framing, mission.
- **Contact**
- **Claim entry point** — "Find your business" search → claim flow (see below).

### B. Business dashboard (post-claim / authenticated)
- **Profile overview** — current published data, claim status, plan tier.
- **Edit / enrich profile** — structured form for all fields (description, hours,
  services/products, pricing, images, etc.).
- **Freshness & coherence report** (Managed tier only) — side-by-side comparison of the
  verified profile vs. what's currently on the business's website / Facebook / OTA
  listings, flagged discrepancies, alert history.
- **AI crawler activity** — visit log by bot (GPTBot, ClaudeBot, PerplexityBot, etc.),
  with a persistent, visible disclaimer that this reflects consideration, not
  guaranteed citation.
- **Plan & billing** — current tier, upgrade/downgrade, annual renewal date, invoices.
- **Account/notification settings**

### C. Admin / ops back-office (internal, staff-only)
- **Profiles table** — search/filter by status (auto-generated / claimed / verified /
  managed), country, industry, source data completeness.
- **Claim review queue** — verify ownership claims before they go live.
- **Dispute / complaint queue** — handle "this isn't my business" / "this data is
  wrong" reports (expect this queue to exist and be usable from day one — auto-
  generating unclaimed profiles will generate some inbound disputes by design).
- **Country clearance tracker** — per-country legal review status (not started / in
  review / cleared / excluded-GDPR), gating whether auto-generation is permitted there.
- **Data source pipeline status** — per-country ingestion source health (registry feed,
  OTA scrape, etc.).
- **Customer & subscription management** — MRR, churn, plan mix, basic cohort view.

---

## 4. Core user flows to design in detail

1. **Unclaimed → claimed**: visitor finds their business's auto-generated page →
   clicks "Is this your business?" → ownership verification (email/phone matching
   against the business's registered contact, or a code-based verification) → profile
   editor → plan selection → payment → confirmation.
2. **Free AI Visibility Check** funnel → claim flow handoff.
3. **Managed-tier freshness alert**: system detects a discrepancy → email notification
   → dashboard deep-link to the specific flagged field → owner accepts/corrects.
4. **Admin claim review**: staff reviews a pending claim, approves or rejects, with an
   audit trail.
5. **Dispute intake**: someone (claimed owner or third party) flags incorrect or
   unwanted data → routed into the admin dispute queue → resolution states (corrected /
   removed / rejected-with-reason).

---

## 5. Data model (high-level — propose the real schema yourself)

Sketch entities, don't treat this as final:

- **BusinessProfile**: canonical_id, name, category/industry, country, address,
  description, structured fields (hours, services, price_range, etc.), claim_status
  (unclaimed / pending / claimed / verified), plan_tier (none / registered / managed),
  source_data (per-origin snapshot: registry / OTA / GBP-adjacent / owner-submitted),
  created_at, last_verified_at.
- **DataSource**: profile_id, source_type (facebook / OTA / own_website / registry),
  last_checked_at, current_snapshot, coherence_status.
- **Owner/User**: auth identity, linked profile(s), role (owner / admin / staff).
- **ClaimRequest**: profile_id, requester, verification_method, status, reviewed_by.
- **Subscription**: profile_id, tier, billing_cycle (annual only), status, renewal_date.
- **FreshnessCheckLog**: profile_id, checked_at, discrepancies_found, alert_sent.
- **CrawlerVisitLog**: profile_id, bot_name, timestamp — surfaced in the dashboard with
  the "consideration, not citation" framing.
- **CountryClearance**: country_code, legal_status, gdpr_excluded (bool), cleared_at.

---

## 6. Internationalization

**Launch content in English only**, but build the i18n architecture now so adding
locales later (French next, likely, given Mauritius) is a config/content change, not a
rearchitecture: route-based locale structure (e.g. `/en/...`), all UI strings in
translation files (not hardcoded in components), locale-aware number/currency
formatting from day one even though only English ships.

---

## 7. Non-functional requirements

- **Server-rendered / statically generated** public pages — this is non-negotiable
  given the product's own thesis about AI crawlability.
- **schema.org structured data** on every business profile page and on key marketing
  pages.
- **WCAG AA accessibility.**
- **Mobile-first responsive.**
- **SEO fundamentals**: sitemap.xml, meta tags, OpenGraph.
- **Auth**: passwordless/magic-link or standard email+password acceptable — propose
  what fits the chosen stack; claim-flow ownership verification is a separate concern
  from account auth, design both.
- **Payments**: plan for annual-billing subscription integration (e.g., Stripe) —
  scaffold the integration point, but full payment implementation can be a later phase
  if you judge it out of scope for this session.

---

## 8. Explicitly out of scope for this phase

- The actual backend crawler/cron pipeline that auto-generates profiles from external
  data sources (separate backend project — this session is front-end + admin UI only,
  assume that data arrives via an API you can design the contract for).
- Real per-country legal clearance work (the app only needs a status-tracking UI for
  it).
- Full payment processing implementation (scaffold only, per above).

---

## 9. What I want out of this Plan Mode session

Do **not** start writing production code. Produce:

1. **Recommended tech stack, with justification** — you choose; weigh SSR/SSG
   capability, i18n support, build speed for a small/solo team, and long-term
   maintainability. Prefer proven, boring technology over novelty.
2. **Proposed repo/folder structure.**
3. **Full sitemap** confirming/refining section 3 above.
4. **Page-by-page component breakdown** for the marketing site, dashboard, and admin
   back-office.
5. **Data model proposal**, refining section 5.
6. **Design tokens**: color palette, type scale, spacing scale — inspired by the
   direction in section 2.
7. **A phased build plan** (milestones), not a single monolithic task list.
8. **Open questions or assumptions** you need me to confirm before implementation
   begins.

Ask me clarifying questions now if anything above is ambiguous before you produce the
plan.
