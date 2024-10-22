<?php

return [
    /*
     * Current Version
     *
     * @var string
     */
    'version' => '9.3.5',
    'version_installed' => '9.3.5',
    'version_db' => '20240910000000', // the key of the latest database migration

    /*
     * Installation status
     *
     * @var bool
     */
    'installed' => true,

    /*
     * The current Locale
     */
    'locale' => 'en_US',

    /*
     * The current Charset
     */
    'charset' => 'UTF-8',

    /*
     * The byte-order-mark for the current charset
     */
    'charset_bom' => "\xEF\xBB\xBF",

    /*
     * Maintenance mode
     */
    'maintenance_mode' => false,

    /*
     * ------------------------------------------------------------------------
     * Debug settings
     * ------------------------------------------------------------------------
     */
    'debug' => [
        /*
         * Display errors
         *
         * @var bool
         */
        'display_errors' => true,

        /*
         * Site debug level
         *
         * @var string (message|debug)
         */
        'detail' => 'debug',

        /*
         * Error reporting level
         *
         * @var int|null
         */
        'error_reporting' => null,

        /**
         * Hide specified superglobal keys and config items from whoops error output
         *
         * By default, all _SERVER, _ENV, and _COOKIE values are hidden
         *
         * @var array<string, string[]>
         */
        'hide_keys' => [
            /** @var string[] */
            '_GET' => [],

            /** @var string[] */
            '_POST' => [],

            /** @var string[] */
            '_FILES' => [],

            /** @var string[] */
            '_SESSION' => [],

            /**
             * Hide specified config keys from whoops error output
             * `concrete.debug.display_errors` will hide that specific config item while `concrete.debug` will hide
             * all items in the `concrete.debug` array.
             *
             * @var string[]
             */
            'config' => [
                'concrete.proxy.password',
                'concrete.mail.methods.smtp.password',
                'concrete.email.default.address',
                'concrete.email.form_block.address',
                'concrete.email.forgot_password.address',
                'concrete.email.validate_registration.address',
                'concrete.email.workflow_notification.address',
                'concrete.debug.hide_keys',
                'app.api_keys',
            ],
        ],
    ],

    /*
     * ------------------------------------------------------------------------
     * Proxy Settings
     * ------------------------------------------------------------------------
     */
    'proxy' => [
        'host' => null,
        'port' => null,
        'user' => null,
        'password' => null,
    ],

    /*
     * ------------------------------------------------------------------------
     * File upload settings
     * ------------------------------------------------------------------------
     */
    'upload' => [
        /*
         * Allowed file extensions
         *
         * @var string semi-colon separated.
         */
        'extensions' => '*.flv;*.jpg;*.gif;*.jpeg;*.ico;*.docx;*.xla;*.png;*.psd;*.swf;*.doc;*.txt;*.xls;*.xlsx;' .
            '*.csv;*.pdf;*.tiff;*.rtf;*.m4a;*.mov;*.wmv;*.mpeg;*.mpg;*.wav;*.3gp;*.avi;*.m4v;*.mp4;*.mp3;*.qt;*.ppt;' .
            '*.pptx;*.kml;*.xml;*.svg;*.webm;*.webp;*.ogg;*.ogv',
        /*
         * Disallowed file extension list (takes the precedence over the extensions allowlist).
         *
         * @var string semi-colon separated.
         */
        'extensions_denylist' => '*.php;*.php2;*.php3;*.php4;*.php5;*.php7;*.php8;*.phtml;*.phar;*.htaccess;*.pl;*.phpsh;*.pht;*.shtml;*.cgi',

        /*
         * Number of maximum parallel uploads
         */
        'parallel' => 4,

        'chunking' => [
            // Enable uploading files in chunks?
            'enabled' => false,
            // The chunk size (if empty we'll automatically determine it)
            'chunkSize' => null,
        ],
    ],

    /*
     * ------------------------------------------------------------------------
     * Export settings
     * ------------------------------------------------------------------------
     */
    'export' => [
        'csv' => [
            // Include the BOM (byte-order mark) in generated CSV files?
            // @var bool
            'include_bom' => false,
            'datetime_format' => 'ATOM',
        ],
    ],

    /*
     * ------------------------------------------------------------------------
     * Interface settings
     * ------------------------------------------------------------------------
     */
    'interface' => [
        'panel' => [
            /*
             * Enable the page relations panel
             */
            'page_relations' => false,
        ],
    ],

    /*
     * ------------------------------------------------------------------------
     * Mail settings
     * ------------------------------------------------------------------------
     */
    'mail' => [
        'method' => 'PHP_MAIL',
        'methods' => [
            'smtp' => [
                'server' => '',
                'port' => '',
                'username' => '',
                'password' => '',
                'encryption' => '',
                'messages_per_connection' => null,
                // The domain to be used in the HELO/EHLO step (if empty we'll use localhost)
                'helo_domain' => 'localhost',
            ],
        ],
    ],

    /*
     * ------------------------------------------------------------------------
     * Cache settings
     * ------------------------------------------------------------------------
     */
    'cache' => [
        /*
         * Enabled
         *
         * @var bool
         */
        'enabled' => true,

        /*
         * Lifetime
         *
         * @var int Seconds
         */
        'lifetime' => 21600,

        /*
         * Cache overrides
         *
         * @var bool
         */
        'overrides' => false,

        /*
         * Cache Blocks
         *
         * @var bool
         */
        'blocks' => false,

        /*
         * Cache Assets
         *
         * @var bool
         */
        'assets' => false,

        /*
         * Cache Theme CSS/JS
         *
         * @var bool
         */
        'theme_css' => true,

        /*
         * Cache full page
         *
         * @var bool|string (blocks|all)
         */
        'pages' => false,

        /*
         * Use Doctrine development mode
         *
         * @var bool
         */
        'doctrine_dev_mode' => false,

        /*
         * How long to cache full page
         *
         * @var string
         */
        'full_page_lifetime' => 'default',

        /**
         * Respect lifetime of each block on the page
         *
         * @var bool
         */
        'full_page_lifetime_block' => false,

        /*
         * Custom lifetime value, only used if concrete.cache.full_page_lifetime is 'custom'
         *
         * @var int
         */
        'full_page_lifetime_value' => null,

        /*
         * Calculate the cache key reading the assets contents (true) of the assets modification time (false).
         *
         * @var bool
         */
        'full_contents_assets_hash' => false,

        'directory' => DIR_FILES_UPLOADED_STANDARD . '/cache',
        /*
         * Relative path to the cache directory. If empty it'll be calculated from concrete.cache.directory
         * @var string|null
         */
        'directory_relative' => null,
        'page' => [
            'directory' => DIR_FILES_UPLOADED_STANDARD . '/cache/pages',
            'adapter' => 'file',
        ],

        'levels' => [
            'overrides' => [
                'drivers' => [
                    'core_ephemeral' => [
                        'class' => '\Stash\Driver\Ephemeral',
                        'options' => [],
                    ],

                    'core_filesystem' => [
                        'class' => \Concrete\Core\Cache\Driver\FileSystemStashDriver::class,
                        'options' => [
                            'path' => DIR_FILES_UPLOADED_STANDARD . '/cache/overrides',
                            'dirPermissions' => DIRECTORY_PERMISSIONS_MODE_COMPUTED,
                            'filePermissions' => FILE_PERMISSIONS_MODE_COMPUTED,
                        ],
                    ],
                    'redis' => [
                        'class' => \Concrete\Core\Cache\Driver\RedisStashDriver::class,
                        'options' => [
                            /* Example configuration for servers
                            'servers' => [
                                [
                                    'server' => 'localhost',
                                    'port' => 6379,
                                    'ttl' => 10 //Connection Timeout - not TTL for objects
                                ],
                                [
                                    'server' => 'outside.server',
                                    'port' => 6379,
                                    'ttl' => 10
                                ],
                            ],*/
                            'prefix' => 'concrete_overrides',
                            'database' => 0, // Use different Redis Databases - optional
                        ],
                    ],
                ],
                'preferred_driver' => 'core_filesystem', // Use this to specify a preferred driver
            ],
            'expensive' => [
                'drivers' => [
                    'core_ephemeral' => [
                        'class' => '\Stash\Driver\Ephemeral',
                        'options' => [],
                    ],
                    'core_filesystem' => [
                        'class' => \Concrete\Core\Cache\Driver\FileSystemStashDriver::class,
                        'options' => [
                            'path' => DIR_FILES_UPLOADED_STANDARD . '/cache/expensive',
                            'dirPermissions' => DIRECTORY_PERMISSIONS_MODE_COMPUTED,
                            'filePermissions' => FILE_PERMISSIONS_MODE_COMPUTED,
                        ],
                    ],
                    'redis' => [
                        'class' => \Concrete\Core\Cache\Driver\RedisStashDriver::class,
                        'options' => [
                            'prefix' => 'concrete_expensive',
                            'database' => 0, // Use different Redis Databases - optional
                        ],
                    ],
                ],
                'preferred_driver' => 'core_filesystem', // Use this to specify a preferred driver
            ],
            'object' => [
                'drivers' => [
                    'core_ephemeral' => [
                        'class' => '\Stash\Driver\Ephemeral',
                        'options' => [],
                    ],
                    'redis' => [
                        'class' => \Concrete\Core\Cache\Driver\RedisStashDriver::class,
                        'options' => [
                            'prefix' => 'concrete_object',
                            'database' => 0, // Use different Redis Databases - optional
                        ],
                    ],
                ],
                'preferred_driver' => 'core_ephemeral', // Use this to specify a preferred driver
            ],
        ],

        'clear' => [
            'thumbnails' => false,
        ],
        /**
         * Timestamp of the last time that the cache was cleared, this is used when generating assets.
         */
        'last_cleared' => 1648642409,
    ],

    'design' => [
        'enable_custom' => true,
        'enable_layouts' => true,
    ],

    /*
     * ------------------------------------------------------------------------
     * Queue/Command/Messenger settings
     * ------------------------------------------------------------------------
     */
    'processes' => [
        'logging' => [
            /*
             * Do we log task process output (triggered in the dashboard or in the CLI) to a file somewhere?
             *
             * @var string (none|file)
             */
            'method' => 'none',

            'file' => [
                /*
                 * The directory that holds process logs
                 *
                 * @var string
                 */
                'directory' => '',
            ],
        ],

        'scheduler' => [
            /*
             * Are scheduled tasks available? Scheduled tasks require running a console command every minute.
             *
             * @var bool
             */
            'enable' => false,
        ],

        /**
         * The point after which old completed are automatically removed from the system.
         */
        'delete_threshold' => 7, // days
    ],

    'messenger' => [
        'default_bus' => 'default',

        'buses' => [
            'default' => [
                'default_middleware' => true,
                'middleware' => [],
            ],
        ],

        'routing' => [
            'Concrete\Core\Foundation\Command\AsyncCommandInterface' => ['async'],
        ],

        'transports' => [
            'Concrete\Core\Messenger\Transport\DefaultAsync\DefaultAsyncTransport',
            'Concrete\Core\Messenger\Transport\DefaultAsync\DefaultSyncTransport', // used for tests and advanced configuration
        ],

        'failure' => [
            'default_receiver' => 'failed',
            'transports' => [
                'Concrete\Core\Messenger\Transport\DefaultFailed\DefaultFailedTransport',
            ],
        ],

        'consume' => [
            /**
             * Listener. If set to app, then queueable operations like rescanning files and deleting bulk pages
             * will be polled and executed through browser XHR processes. If set to worker you must run
             * `concrete/bin/messenger:consume` from the command line. This command can be run multiple times to
             * add additional queue workers for command processing.
             *
             * @var string (app|worker)
             */
            'method' => 'app',
        ],

        /*
         * If we're consuming the queue through polling, how many entries do we do at a time
         *
         * @var int
         */
        'polling_batch' => [
            'default' => 10,
            'rescan_file' => 5,
            'delete_page' => 100,
            'delete_page_forever' => 100,
            'copy_page' => 10,
        ],
    ],

    /*
     * ------------------------------------------------------------------------
     * Events settings
     * ------------------------------------------------------------------------
     */
    'events' => [
        'broadcast' => [
            /*
             * Driver
             *
             * @var string (redis|none)
             */
            'driver' => '',
        ],
    ],

    /*
     * ------------------------------------------------------------------------
     * Logging settings
     * ------------------------------------------------------------------------
     */
    'log' => [
        /*
         * Whether to log emails
         *
         * @var bool
         */
        'emails' => true,

        /*
         * Whether to log Errors
         *
         * @var bool
         */
        'errors' => true,

        /*
         * Whether to log Spam
         *
         * @var bool
         */
        'spam' => false,

        /*
         * Whether to log REST API requests headers
         *
         * @var bool
         */
        'api' => false,

        'enable_dashboard_report' => true,

        'configuration' => [
            /*
             * Configuration mode
             *
             * @var string simple|advanced
             */
            'mode' => 'simple',
            'simple' => [
                /*
                 * What log level to store core logs in the database
                 * @var string
                 */
                'core_logging_level' => 'NOTICE',

                /*
                 * Which handle to use
                 *
                 * @var string (database|file)
                 */
                'handler' => 'database',

                'file' => [
                    /*
                     * File path to store logs
                     *
                     * @var string
                     */
                    'file' => '',
                ],
            ],

            'advanced' => [
                'configuration' => [],
            ],
        ],
    ],
    'jobs' => [
        'enable_scheduling' => true,
    ],

    'filesystem' => [
        /* Temporary directory.
         * @link \Concrete\Core\File\Service\File::getTemporaryDirectory
         */
        'temp_directory' => null,
        'permissions' => [
            'file' => FILE_PERMISSIONS_MODE_COMPUTED,
            'directory' => DIRECTORY_PERMISSIONS_MODE_COMPUTED,
        ],
        'download' => [
            'filename_pattern' => '',
        ],
    ],

    /*
     * ------------------------------------------------------------------------
     * Email settings
     * ------------------------------------------------------------------------
     */
    'email' => [
        /*
         * Enable emails
         *
         * @var bool
         */
        'enabled' => true,
        'default' => [
            'address' => '',
            'name' => '',
        ],
        'form_block' => [
            'address' => false,
        ],
        'forgot_password' => [
            'address' => null,
            'name' => null,
        ],
        'register_notification' => [
            'address' => null,
            'name' => null,
        ],
        'validate_registration' => [
            'address' => null,
            'name' => null,
        ],
        'workflow_notification' => [
            'address' => null,
            'name' => null,
        ],
    ],

    /*
     * ------------------------------------------------------------------------
     * Form settings
     * ------------------------------------------------------------------------
     */
    'form' => [
        /*
         * Whether to store form submissions. Auto means form submissions will be stored, but the block
         * will offer an option to disable on a per-block basis. True means they will always be stored,
         * and false means they will never be stored.
         *
         * @var string "auto", true or false
         */
        'store_form_submissions' => 'auto',
    ],

    /*
     * ------------------------------------------------------------------------
     * Marketplace settings
     * ------------------------------------------------------------------------
     */
    'marketplace' => [
        /*
         * Enable marketplace integration
         *
         * @var bool concrete.marketplace.enabled
         */
        'enabled' => true,

        /*
         * Time it takes for a request to timeout
         *
         * @var int concrete.marketplace.request_timeout
         */
        'request_timeout' => 30,

        /*
         * Marketplace Token
         *
         * @var null|string concrete.marketplace.token
         */
        'token' => null,

        /*
         * Marketplace Site url Token
         *
         * @var null|string concrete.marketplace.site_token
         */
        'site_token' => null,

        /*
         * Enable intelligent search integration
         *
         * @var bool concrete.marketplace.intelligent_search
         */
        'intelligent_search' => true,

        /*
         * Log requests
         *
         * @var bool concrete.marketplace.log_requests
         */
        'log_requests' => false,
    ],

    /*
     * ------------------------------------------------------------------------
     * Getting external news and help from concretecms.com
     * ------------------------------------------------------------------------
     */
    'external' => [
        /*
         * Provide help within the intelligent search
         *
         * @var bool concrete.external.intelligent_search_help
         */
        'intelligent_search_help' => true,

        /*
         * Enable Concrete news within your site
         *
         * @var bool concrete.external.news
         */
        'news' => true,
    ],

    /*
     * --------------------------------------------------------------------
     * Miscellaneous settings
     * --------------------------------------------------------------------
     */
    'misc' => [
        'user_timezones' => false,
        'package_backup_directory' => DIR_FILES_UPLOADED_STANDARD . '/trash',
        'display_package_delete_button' => true,
        'enable_progressive_page_reindex' => true,
        'mobile_theme_id' => 0,
        'sitemap_approve_immediately' => true,
        'enable_translate_locale_en_us' => false,
        'page_search_index_lifetime' => 259200,
        'enable_trash_can' => true,
        /*
         * The JPEG compression level (in range 0... 100)
         */
        'default_jpeg_image_compression' => 80,
        /*
         * The WEBP compression level (in range 0... 100)
         */
        'default_webp_image_compression' => 80,
        /*
         * The PNG compression level (in range 0... 9)
         */
        'default_png_image_compression' => 9,
        /*
         * The default thumbnail format: jpeg, png, webp, auto
         * (if auto: we'll keep original format if it is supported, we'll create a webp otherwise).
         */
        'default_thumbnail_format' => 'auto',
        /*
         * The threshold (total number of pixels - width x height x number of frames)
         * after which we'll reload images instead of creating in-memory clones.
         * If empty: unlimited
         */
        'inplace_image_operations_limit' => 4194304,
        /*
         * @var string (now|async)
         */
        'basic_thumbnailer_generation_strategy' => 'now',
        'help_overlay' => true,
        'require_version_comments' => false,
        /*
        * Changes the "Keep Live Version Approved"-Toggle-Button behaviour on the site page version scheduling element
        * unapproved: removes live or scheduled version of the site page, if the changed site page gets scheduled
        * approved: keeps live or scheduled version of the site page approved, if the changed site page gets scheduled
        *
        * @var string (approved | unapproved)
        */
        'live_version_status_on_scheduled_version_approval' => 'unapproved',
        /*
         * Control whether a block type can me moved to different block type sets
         *
         * @var bool
         */
        'enable_move_blocktypes_across_sets' => false,
        /*
         * Check whether to add a "generator" tag with the Concrete version to the site pages
         *
         * @var bool
         */
        'generator_tag_display_in_header' => true,
    ],

    'theme' => [
        'compress_preprocessor_output' => true,
        'generate_less_sourcemap' => false,
    ],

    'updates' => [
        'enable_auto_update_packages' => false,
        'enable_permissions_protection' => true,
        'check_threshold' => 172800,
        'services' => [
            'get_available_updates' => 'https://www.concretecms.com/api/remote_update/update_core',
            'inspect_update' => 'https://www.concretecms.com/api/remote_update/inspect_update',
        ],
        // Set to true to skip checking if there's a newer core version available (useful for example if the core is upgraded via composer)
        'skip_core' => false,
        // List of package handles that shouldn't be checked for new versions in marketplace (useful for example if the core is upgraded via composer)
        // Set to true to skip all the packages
        'skip_packages' => [],
    ],
    'paths' => [
        'trash' => '/!trash',
        'drafts' => '/!drafts',
    ],
    'icons' => [
        'page_template' => [
            'width' => 120,
            'height' => 90,
        ],
        'theme_thumbnail' => [
            'width' => 120,
            'height' => 90,
        ],
        'file_manager_listing' => [
            'handle' => 'file_manager_listing',
            'width' => 120,
            'height' => 120,
        ],
        'file_manager_detail' => [
            'handle' => 'file_manager_detail',
            'width' => 500,
            'height' => 500,
        ],
        'user_avatar' => [
            'width' => 120,
            'height' => 120,
            'default' => ASSETS_URL_IMAGES . '/avatar_none.png',
            'resolution' => '2', // Set this to 1 if you want width and height to match in the cropper exactly.
        ],
    ],

    'file_manager' => [
        'images' => [
            'use_exif_data_to_rotate_images' => false,
            'manipulation_library' => 'gd',
            'create_high_dpi_thumbnails' => true,
            /*
             * The style of preview image used in the file_manager
             *
             * @var string 'small'(default,30x30), 'large(60x60)' or 'full(size of file_manager_listing)'
             */
            'preview_image_size' => 'small',
            /*
             * Show file_manager_detail thumbnail as preview image in popover
             *
             * @var boolean
             */
            'preview_image_popover' => true,
            // SVG sanitization
            'svg_sanitization' => [
                // The operation that the SVG sanitizer should perform.
                // This must be value of one of the Concrete\Core\File\Import\Processor\SvgProcessor::ACTION_... constants
                'action' => 'sanitize',
                // Space-separated list of tags to be kept
                'allowed_tags' => '',
                // Space-separated list of attributes to be kept
                'allowed_attributes' => '',
            ],
        ],
        /*
         * Options for the results per page dropdown
         *
         * @var array
         */
        'items_per_page_options' => [10, 25, 50, 100, 250],
        /*
         * Default number of results per page
         *
         * @var int
         */
        'results' => 50,
         /*
          * The maximim width (in pixels) for the uploaded images
          */
        'restrict_max_width' => null,
        /*
         * The maximim height (in pixels) for the uploaded images
         */
        'restrict_max_height' => null,
        /*
         * Don't resize the files with these mime types (space-separated list)
         */
        'dont_resize_mimetypes' => 'image/gif',
        /**
         * Enable asciify to sanitize name of uploaded files
         */
        'enable_filename_asciify' => true,
        /**
         * Determines whether the list should keep folders on top when sorting by name.
         */
        'keep_folders_on_top' => false,
    ],

    'search_users' => [
        'results' => 10,
    ],

    'sitemap_xml' => [
        'file' => 'sitemap.xml',
        'frequency' => 'weekly',
        'priority' => 0.5,
    ],

    /*
     * ------------------------------------------------------------------------
     * Accessibility
     * ------------------------------------------------------------------------
     */
    'accessibility' => [
        /*
         * Show titles in the toolbars
         *
         * @var bool
         */
        'toolbar_titles' => false,

        /*
         * Increase the font size in the toolbars
         *
         * @var bool
         */
        'toolbar_large_font' => false,

        /*
         * Show tooltips in the toolbars
         *
         * @var bool
         */
        'toolbar_tooltips' => true,
    ],

    /*
     * ------------------------------------------------------------------------
     * Internationalization
     * ------------------------------------------------------------------------
     */
    'i18n' => [
        /*
         * Allow users to choose language on login
         *
         * @var bool
         */
        'choose_language_login' => false,
        // Fetch language files when installing a package connected to the marketplace [boolean]
        'auto_install_package_languages' => true,
        // Community Translation instance offering translations
        'community_translation' => [
            // API entry point of the Community Translation instance
            'entry_point' => 'https://translate.concretecms.org/api',
            // API Token to be used for the Community Translation instance
            'api_token' => '',
            // Languages below this translation progress won't be considered
            'progress_limit' => 60,
            // Lifetime (in seconds) of the cache items associated to downloaded data
            'cache_lifetime' => 3600, // 1 hour
            // Base URI for package details
            'package_url' => 'https://translate.concretecms.org/translate/package',
        ],
    ],
    'urls' => [
        'concrete_community' => 'https://community.concretecms.com',
        'package_repository' => 'https://dl.market.concretecms.com',
        'marketplace' => 'https://market.concretecms.com',
        'background_feed' => 'https://backgroundimages.concretecms.com/wallpaper',
        'privacy_policy' => '//www.concretecms.com/about/legal/privacy-policy',
        'background_info' => 'https://backgroundimages.concretecms.com/get_image_data.php',
        'videos' => 'https://www.youtube.com/user/concrete5cms/videos',
        'activity_slots' => 'https://marketing.concretecms.com/ccm/marketing/activity_slots',
        'help' => [
            'developer' => 'https://documentation.concretecms.org/developers',
            'user' => 'https://documentation.concretecms.org/user-guide',
            'forum' => 'https://forums.concretecms.org',
            'support' => 'https://www.concretecms.com/support/hiring-help',
            'remote_search' => 'https://documentation.concretecms.org/ccm/documentation/remote_search',
        ],
        'paths' => [
            'package_repository' => [
                'connect' => '/concrete/connect',
                'connect_validate' => '/concrete/connect/validate',
                'update' => '/concrete/update',
                'register_url' => '/concrete/connect/register_url',
                'list' => '/concrete/public/list',
                'get' => '/concrete/public/package/%s',
            ],
            'marketplace' => [
                'connect' => '/depot/connect',
                'projects' => '/account/sites/details',
            ],
        ],
    ],

    /*
     * ------------------------------------------------------------------------
     * White labeling.
     * ------------------------------------------------------------------------
     */
    'white_label' => [
        /*
         * Custom Logo source path relative to the public directory.
         *
         * @var bool|string The logo path
         */
        'logo' => false,

        /*
         * Custom Name
         *
         * @var bool|string The name
         */
        'name' => false,

        /*
         * Controls how we show the background image on the login/other concrete pages. None = no image, Feed =
         * standard behavior, "custom" = custom image.
         *
         * @var string "none"|"feed"|"custom"
         */
        'background_image' => 'feed',

        /*
         * If the background image is custom, this is where it loads from.
         *
         * @var null|string Custom URL for background image.
         */
        'background_url' => null,
    ],
    'session' => [
        'name' => 'CONCRETE',
        'handler' => 'file',
        'redis' => [
            'database' => 1, // Use different Redis Databases - optional
        ],
        'save_path' => null,
        // Minimum duration (in seconds) of an "unoutched" session
        'max_lifetime' => 7200,
        // gc_probability and gc_divisor together define the probability to
        // cleanup expided sessions ("garbage collection").
        // Example: if gc_probability is 1 and gc_divisor is 100, on average we'll have 1 GC every 100 requests (1%)
        // Example: if gc_probability is 5 and gc_divisor is 20, on average we'll have 1 GC every 20 requests (25%)
        'gc_probability' => 1,
        'gc_divisor' => 100,
        'cookie' => [
            'cookie_path' => false, // set a specific path here if you know it, otherwise it'll default to relative
            'cookie_lifetime' => 0,
            'cookie_domain' => false,
            // true: enable the 'secure' flag; false: disable the secure flag; null: enable the 'secure' flag for https requests only
            'cookie_secure' => null,
            'cookie_httponly' => true,
            'cookie_raw' => false,
            'cookie_samesite' => null,
        ],
        'remember_me' => [
            'lifetime' => 1209600, // 2 weeks in seconds
        ],
    ],

    /*
     * ------------------------------------------------------------------------
     * User information and registration settings.
     * ------------------------------------------------------------------------
     */
    'user' => [
        /*
         * --------------------------------------------------------------------
         * Registration settings.
         * --------------------------------------------------------------------
         */
        'registration' => [
            /*
             * Registration
             *
             * @var bool
             */
            'enabled' => false,

            /*
             * Registration type
             *
             * @var string The type (disabled|enabled|validate_email)
             */
            'type' => 'disabled',

            /*
             * Enable Registration Captcha
             *
             * @var bool
             */
            'captcha' => true,

            /*
             * Use emails instead of usernames to log in
             *
             * @var bool
             */
            'email_registration' => false,

            /*
             * Determines whether the username field is displayed when registering
             */
            'display_username_field' => true,

            /*
             * Determines whether the confirm password field is displayed when registering
             */
            'display_confirm_password_field' => true,

            /*
             * Validate emails during registration
             *
             * @var bool
             */
            'validate_email' => false,

            /**
             * Threshold in seconds to delete unvalidated users
             *
             * @see \Concrete\Core\Command\Task\Controller\RemoveUnvalidatedUsersController
             * @var int Seconds
             */
            'validate_email_threshold' => 5184000, // 60 days

            /*
             * Admins approve each registration
             *
             * @var bool
             */
            'approval' => false,

            /*
             * Send notifications after successful registration.
             *
             * @var bool|string Email to notify
             */
            'notification' => false,
        ],

        /*
         * --------------------------------------------------------------------
         * Edit Profile form settings.
         * --------------------------------------------------------------------
         */
        'edit_profile' => [
            /*
             * Determines whether the username field is displayed when editing profile
             */
            'display_username_field' => true,
        ],

        /*
         * --------------------------------------------------------------------
         * Group Settings
         * --------------------------------------------------------------------
         */
        'group' => [
            'badge' => [
                'default_point_value' => 50,
            ],
            'delete_requires_superuser' => true,
        ],

        'username' => [
            'maximum' => 64,
            'minimum' => 3,
            'allowed_characters' => [
                'boundary' => 'A-Za-z0-9',
                'middle' => 'A-Za-z0-9_\.',
                'requirement_string' => 'A username may only contain letters, numbers, dots (not at the beginning/end), and underscores (not at the beginning/end).',
                'error_string' => 'A username may only contain letters, numbers, dots (not at the beginning/end), and underscores (not at the beginning/end).',
            ],
        ],
        'password' => [
            'maximum' => 128,
            'minimum' => 5,
            'required_special_characters' => 0,
            'required_lower_case' => 0,
            'required_upper_case' => 0,
            'reuse' => 0,
            'max_age' => null, // Max age (in days) before users have to change their password
            'custom_regex' => [],

            /**
             * Using PASSWORD_DEFAULT means that we will automatically switch to better algorithms when they are available.
             * Keep in mind hash_options are different depending on the algorithm specified
             * @see https://www.php.net/manual/en/password.constants.php
             */
            'hash_algorithm' => PASSWORD_DEFAULT,
            'hash_options' => [
                // 'cost' => '12', // Bcrypt cost
                // 'memory_cost' => '1024', // Argon2 memory cost in bytes
                // 'time_cost' => '10', // Argon2 time cost in milliseconds
            ],

            /**
             * @deprecated This setting is no longer used by the core.
             */
            'hash_portable' => false,

            /**
             * @deprecated This setting is no longer used by the core, use hash_options instead.
             */
            'hash_cost_log2' => 12,

            'legacy_salt' => '',
            'reset_message' => [
                'password_reset' => '',
                'password_expired' => '',
            ],
        ],
        'email' => [
            'test_mx_record' => false,
            'strict' => true,
        ],
        'private_messages' => [
            'throttle_max' => 20,
            'throttle_max_timespan' => 15, // minutes
        ],

        'deactivation' => [
            'enable_login_threshold_deactivation' => false,
            'login' => [
                'threshold' => 120, // in days
            ],
            'authentication_failure' => [
                'enabled' => false,
                'amount' => 5, // The number of failures
                'duration' => 300, // In so many seconds
            ],
            'message' => 'This user is inactive. Please contact us regarding this account.',
        ],
    ],

    /*
     * ------------------------------------------------------------------------
     * Spam
     * ------------------------------------------------------------------------
     */
    'spam' => [
        /*
         * Allowlist group ID
         *
         * @var int
         */
        'allowlist_group' => 0,

        /*
         * Notification email
         *
         * @var string
         */
        'notify_email' => '',
    ],

    /*
     * ------------------------------------------------------------------------
     * Calendar
     * ------------------------------------------------------------------------
     */
    'calendar' => [
        'colors' => [
            'text' => '#ffffff',
            'background' => '#3A87AD',
        ],
    ],

    /*
     * ------------------------------------------------------------------------
     * Security
     * ------------------------------------------------------------------------
     */
    'security' => [
        'session' => [
            'invalidate_on_user_agent_mismatch' => true,

            'invalidate_on_ip_mismatch' => true,

            'ignored_ip_mismatches' => [],

            'enable_user_specific_ignored_ip_mismatches' => false,

            'invalidate_inactive_users' => [
                // Is the automatically logout inactive users setting enabled?
                'enabled' => false,
                // Time window (in seconds) for inactive users to be automatically logout
                'time' => 300,
            ],
        ],
        'production' => [
            'mode' => 'production',
            'staging' => [
                // Whether to always show the staging notification bar, even for logged-out users.
                'show_notification_to_unregistered_users' => false,
            ],
        ],
        'misc' => [
            /**
             * Content Security Policy (CSP) HTTP response header
             * A modern way to protect from cross-site scripting attacks.
             * Highly recommended to set a rule for your website.
             *
             * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy
             *
             * @var bool|string|string[] CSP policies. Allowed to set multiple policies by an array.
             */
            'content_security_policy' => false,

            /**
             * Strict-Transport-Security (HSTS) HTTP response header
             * This header informs the browser to load always https:// pages.
             * You can set a policy if your site always accessible on SSL.
             *
             * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Strict-Transport-Security
             *
             * @var bool|string
             */
            'strict_transport_security' => false,

            /**
             * X-Frame-Options HTTP response header
             * Protect from click-jacking attacks by blocking your site embedded into other sites.
             *
             * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options
             *
             * @var bool|string DENY, SAMEORIGIN, ALLOW-FROM uri
             */
            'x_frame_options' => 'SAMEORIGIN',
        ],
    ],

    /*
     * ------------------------------------------------------------------------
     * Permissions and behaviors toggles.
     * ------------------------------------------------------------------------
     */
    'permissions' => [
        /*
         * Forward to login if access is denied
         *
         * @var bool
         */
        'forward_to_login' => true,

        /*
         * Permission model
         *
         * @var string The permission model (simple|advanced)
         */
        'model' => 'simple',
    ],

    /*
     * ------------------------------------------------------------------------
     * SEO Settings
     * ------------------------------------------------------------------------
     */
    'seo' => [
        'exclude_words' => 'a, an, as, at, before, but, by, for, from, is, in, into, like, of, off, on, onto, per, ' .
            'since, than, the, this, that, to, up, via, with',
        'enable_slug_asciify' => true,

        /*
         * URL rewriting
         *
         * Doesn't impact concrete.seo.url_rewriting_all which is set at a lower level and
         * controls whether ALL items will be rewritten.
         *
         * @var bool
         */
        'url_rewriting' => false,
        'url_rewriting_all' => false,
        'redirect_to_canonical_url' => false,
        'canonical_url' => null,
        'canonical_url_alternative' => null,
        'trailing_slash' => false,
        'title_format' => '%2$s :: %1$s',
        'title_segment_separator' => ' :: ',
        'page_path_separator' => '-',
        'group_name_separator' => ' / ',
        'segment_max_length' => 128,
        'paging_string' => 'ccm_paging_p',
    ],

    /*
     * ------------------------------------------------------------------------
     * Statistics Settings
     * ------------------------------------------------------------------------
     */
    'statistics' => [
        'track_downloads' => true,
    ],
    'limits' => [
        'sitemap_pages' => 100,
        'page_search_index_batch' => 200,
        'job_queue_batch' => 10,
        'style_customizer' => [
            'size_min' => -50,
            'size_max' => 200,
        ],
    ],

    'page' => [
        'search' => [
            // Always reindex pages (usually it isn't performed when approving workflows)
            'always_reindex' => false,
        ],
    ],

    'editor' => [
        'plugins' => [
            'selected' => [],
        ],
    ],

    'composer' => [
        // [float] The time in seconds until idle triggers a save (set to 0 to disable autosave)
        'idle_timeout' => 1,
    ],

    /*
     * ------------------------------------------------------------------------
     * API settings
     * ------------------------------------------------------------------------
     */
    'api' => [
        /*
         * Enabled
         *
         * @var bool
         */
        'enabled' => false,

        /**
         * Which grant types do we allow to connect to the API.
         *
         * @var array
         */
        'grant_types' => [
            'client_credentials' => true,
            'authorization_code' => true,
            'password_credentials' => false,
            'refresh_token' => true,
        ],
    ],

    /*
     * ------------------------------------------------------------------------
     * Notification settings
     * ------------------------------------------------------------------------
     */
    'notification' => [
        /*
         * Enable Server-Sent Events?
         *
         * @var bool
         */
        'server_sent_events' => false,

        'mercure' => [
            'jwt' => [
                'publisher' => [
                    'expires_at' => '+30 minutes',
                ],
                'subscriber' => [
                    'expires_at' => '+30 minutes',
                ],
            ],
        ],
    ],

    'mutex' => [
        'semaphore' => [
            'priority' => 100,
            'class' => Concrete\Core\System\Mutex\SemaphoreMutex::class,
        ],
        'file_lock' => [
            'priority' => 50,
            'class' => Concrete\Core\System\Mutex\FileLockMutex::class,
        ],
    ],

    'social' => [
        'additional_services' => [
            // Add here a list of arrays like this:
            // ['service_handle', 'Service Name', 'icon']
            // Where 'icon' is the handle of a FontAwesome 4 icon (see https://fontawesome.com/v4.7.0/icons/ )
        ],
    ],

    'file_chooser' => [
        'results' => 20,
    ]
];
