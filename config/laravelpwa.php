<?php

return [
    'name' => 'لوحة تحكم نعيمي الشمال',
    'manifest' => [
        'name' => "لوحة التحكم",
        'short_name' => 'لوحة التحكم',
        'start_url' => '/admin',
        'background_color' => '#ea580b',
        'theme_color' => '#ea580b',
        'display' => 'standalone',
        'orientation'=> 'any',
        'status_bar'=> 'black',
        'icons' => [

            '512x512' => [
                'path' => '/images/icons/icon-512X512-maskable.png',
                'purpose' => 'maskable'
            ],
            '511x511' => [
                'path' => '/images/icons/icon-512X512-rounded.png',
                'purpose' => 'any'
            ],
        ],
        'splash' => [
            '640x1136' => '/images/icons/splash-640x1136.png',
            '750x1334' => '/images/icons/splash-750x1334.png',
            '828x1792' => '/images/icons/splash-828x1792.png',
            '1125x2436' => '/images/icons/splash-1125x2436.png',
            '1242x2208' => '/images/icons/splash-1242x2208.png',
            '1242x2688' => '/images/icons/splash-1242x2688.png',
            '1536x2048' => '/images/icons/splash-1536x2048.png',
            '1668x2224' => '/images/icons/splash-1668x2224.png',
            '1668x2388' => '/images/icons/splash-1668x2388.png',
            '2048x2732' => '/images/icons/splash-2048x2732.png',
        ],
        'shortcuts' => [
        ],
        'custom' => []
    ]
];
