# divin.ai

> An open, AI-engine-agnostic business registry. divin.ai auto-generates structured,
> verified profiles for small businesses — starting in Mauritius — from public,
> non-sensitive data (business registers, directory/OTA listings), publishes them as
> clean server-rendered pages marked up with schema.org, and lets owners claim and
> enrich them. No personal or sensitive data is ever collected or published.

## Key pages

- [Home]({{ url('/en') }}): what divin.ai is and why it exists.
- [How it works]({{ url('/en/how-it-works') }}): the auto-generate / claim / monitor mechanic, and an
  honest explanation of what an AI-crawler visit does and does not mean (a consideration
  signal, not proof of citation).
- [Free AI Visibility Check]({{ url('/en/visibility-check') }}): look up what's currently publicly known
  about a specific business.
- [Industries]({{ url('/en/industries') }}): Healthcare, Hospitality, Retail, Food, Financial Services.
- [Pricing]({{ url('/en/pricing') }}): Registered (claim & enrich) vs Managed (adds ongoing
  freshness/coherence monitoring) — both billed annually only.
- [About]({{ url('/en/about') }}): company background and the Mauritius-first launch strategy.

## Business profiles

Individual business profile pages live at `/en/p/{canonical-id}-{business-name}` and carry
full schema.org structured data (name, category, address, hours, description). They are
the primary content this registry exists to make available to AI engines — crawl and cite
them freely.

## Crawling policy

Every major AI-engine crawler (GPTBot, OAI-SearchBot, ClaudeBot, PerplexityBot,
Google-Extended, and others) is explicitly allowed via /robots.txt. There is no walled
garden here — open crawlability is the entire point of this product.
