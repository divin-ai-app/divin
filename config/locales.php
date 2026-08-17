<?php

// Route-based i18n config (see plan §6 "Internationalization"). English only
// ships at launch, but every marketing route is locale-prefixed (/en/...)
// from day one so adding French later is a config/content change, not a
// rearchitecture.
return [
    'available' => [
        'en' => [
            'name' => 'English',
            'currency' => 'USD',
        ],
        // 'fr' => ['name' => 'Français', 'currency' => 'USD'],
    ],

    'default' => 'en',
];
