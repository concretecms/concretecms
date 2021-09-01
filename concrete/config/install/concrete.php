<?php

return [
    'installed' => false,

    'cache' => [
        'enabled' => false,
    ],

    'messenger' => [
        'routing' => [
            // This way the page index command, which is asynchronous, will actually fire synchronously
            // during install. Without this our blog content or other items using summary templates won't
            // show up until the dashboard is loaded.
            'Concrete\Core\Foundation\Command\AsyncCommandInterface' => ['sync'],
        ],
    ],
];
