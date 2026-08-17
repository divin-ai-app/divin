<?php

// Data-driven industry pages (plan §3 "Industries hub + 5 individual pages").
// Kept as a single config array rather than 5 near-duplicate Blade views —
// resources/views/marketing/industries/show.blade.php renders whichever
// slug matches. Add a 6th industry here later without touching routes/views.
return [
    'healthcare' => [
        'name' => 'Healthcare',
        'tagline' => 'When an AI engine gets your clinic\'s hours or address wrong, that\'s not a bad review — it\'s a liability risk.',
        'pain_points' => [
            'Patients ask ChatGPT "is this clinic open now" before they call — a stale Facebook Page answer sends them to a competitor, or to your locked door.',
            'Clinics rarely control what OTA-style directory listings say about the services they actually offer.',
            'Multiple locations means multiple sources of truth, and they drift out of agreement constantly.',
        ],
        'example' => 'A dental clinic in Grand Baie changes its Saturday hours. Its Facebook Page is updated same day; its old business-directory listing isn\'t. Six weeks later, an AI assistant is still citing the old hours to a patient standing outside a locked door.',
        'icon' => 'heart-pulse',
    ],
    'hospitality' => [
        'name' => 'Hospitality',
        'tagline' => 'Hotels and guesthouses live or die by AI-engine discoverability now — most have no site an AI crawler can actually read.',
        'pain_points' => [
            'Independent guesthouses often have only a Facebook Page and an OTA listing — both thin, both partly blocked to AI crawlers.',
            'Room types, amenities, and seasonal pricing change often; AI engines keep citing whatever was last crawlable.',
            '"Book direct" pages rarely exist in a form any AI engine can parse into a clean answer.',
        ],
        'example' => 'A guesthouse in Grand Baie renovates and adds a pool. Its OTA listing photo set updates; its own presence — the thing an AI engine would actually crawl — never mentions the pool at all.',
        'icon' => 'building-2',
    ],
    'retail' => [
        'name' => 'Retail',
        'tagline' => 'If an AI engine can\'t tell what you sell, it recommends the store down the street that it can read.',
        'pain_points' => [
            'Small retailers rarely publish structured product/category data anywhere crawlable.',
            'Store hours around public holidays are usually announced only as a Facebook post, which disappears from view within days.',
            'Multi-branch retailers need each location distinguished clearly — AI engines conflate branches when data is thin.',
        ],
        'example' => 'A boutique posts "closed for stock-take" on Facebook for three days. An AI engine, unable to crawl that post, keeps telling shoppers the store is open.',
        'icon' => 'shopping-bag',
    ],
    'food' => [
        'name' => 'Food',
        'tagline' => 'Restaurants get asked about via AI more than via search now — menus, dietary options, and hours need a source an AI engine can actually reach.',
        'pain_points' => [
            'Menus live in PDF or Facebook photo albums — unreadable to most AI crawlers.',
            'Dietary/allergen information is exactly the kind of question people now ask an AI assistant first.',
            'Reservation and walk-in policies change seasonally and are rarely documented anywhere structured.',
        ],
        'example' => 'A restaurant adds a vegan menu. A diner asks an AI assistant for vegan options nearby; the assistant, working only from an old cached summary, says the restaurant has none.',
        'icon' => 'utensils',
    ],
    'financial-services' => [
        'name' => 'Financial Services',
        'tagline' => 'Real estate agencies and financial services firms are judged on accuracy — AI engines citing outdated details cost trust, not just clicks.',
        'pain_points' => [
            'Licensing, service areas, and contact details need to be exactly right — an AI engine repeating an old office address is a real problem.',
            'Property/service listings change constantly and rarely have one clean, crawlable source of truth.',
            'Trust signals (verification, accreditation) are hard to convey through a Facebook Page alone.',
        ],
        'example' => 'A real estate agency relocates offices. Months later, an AI assistant is still directing prospective clients to the old address because that\'s the last place it found the information published.',
        'icon' => 'landmark',
    ],
];
