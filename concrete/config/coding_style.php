<?php

return [
    'php' => [
        'filter' => [
            // Whitespace-separated list of file extensions to be parsed
            'extensions' => 'php',
            // Whitespace-separated list of additional files (relative to the webroot) to be parsed
            'include' => 'concrete/bin/concrete',
        ],
        'ignore_directories' => [
            // Whitespace-separated list of directory names that should not be parsed
            'by_name' => 'vendor',
            // Whitespace-separated list of directory paths (relative to the webroot) that should not be parsed (allowed placeholders: <HANDLE>)
            'by_path' => 'application/config/doctrine application/files tests/assets',
        ],
        // Whitespace-separated list of file paths (relative to the webroot) that are executed before checking the PHP version.
        'bootstrap_files' => 'index.php concrete/dispatcher.php concrete/bin/concrete',
        'php_only' => [
            'non_psr4' => [
                // Whitespace-separated list of file paths (relative to the webroot) that only contain PHP and that don't follow PSR-4 class names (allowed placeholders: <HANDLE>).
                'files' => <<<'EOT'
concrete/attributes/<HANDLE>/controller.php
application/attributes/<HANDLE>/controller.php
packages/<HANDLE>/attributes/<HANDLE>/controller.php

packages/<HANDLE>/controller.php

concrete/authentication/<HANDLE>/controller.php
application/authentication/<HANDLE>/controller.php
packages/<HANDLE>/authentication/<HANDLE>/controller.php

concrete/blocks/<HANDLE>/controller.php
application/blocks/<HANDLE>/controller.php
packages/<HANDLE>/blocks/<HANDLE>/controller.php

concrete/geolocation/<HANDLE>/controller.php
application/geolocation/<HANDLE>/controller.php
packages/<HANDLE>/geolocation/<HANDLE>/controller.php

concrete/themes/<HANDLE>/page_theme.php
application/themes/<HANDLE>/page_theme.php
packages/<HANDLE>/themes/<HANDLE>/page_theme.php
EOT
                ,
                // Whitespace-separated list of directory paths (relative to the webroot) that contain PHP-only files that don't follow PSR-4 class names (allowed placeholders: <HANDLE>).
                'directories' => <<<'EOT'
concrete/controllers
application/controllers
packages/<HANDLE>/controllers

concrete/jobs
application/jobs
packages/<HANDLE>/jobs
EOT
                ,
            ],
            'psr4' => [
                // Whitespace-separated list of file paths (relative to the webroot) that only contain PHP and that follow PSR-4 class names (allowed placeholders: <HANDLE>).
                'files' => <<<'EOT'
EOT
                ,
                // Whitespace-separated list of directory paths (relative to the webroot) that contain PHP-only files that don't follow PSR-4 class names (allowed placeholders: <HANDLE>).
                'directories' => <<<'EOT'
concrete/bootstrap
application/bootstrap

concrete/config
application/config
packages/<HANDLE>/config

concrete/routes
application/routes
packages/<HANDLE>/routes

concrete/src
application/src
packages/<HANDLE>/src

tests
EOT
                ,
            ],
        ],
    ],
];
