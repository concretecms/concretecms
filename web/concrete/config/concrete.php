<?php

return array(

    /**
     * Current Version
     *
     * @var string
     */
    'version'           => '5.7.4.1',
    'version_installed' => '5.7.4.1',
    'version_db' => '20150504000000', // the key of the latest database migration - corresponds to 5.7.4RC2

    /**
     * Installation status
     *
     * @var bool
     */
    'installed'         => true,

    /**
     * The current Site Name
     *
     * @var string concrete.core.site
     */
    'site'              => 'concrete5',

    /**
     * The current Locale
     */
    'locale'            => 'en_US',

    /**
     * The current Charset
     */
    'charset'           => 'UTF-8',

    /**
     * Maintenance mode
     */
    'maintenance_mode'  => false,

    /**
     * ------------------------------------------------------------------------
     * Debug settings
     * ------------------------------------------------------------------------
     */
    'debug'             => array(
        /**
         * Display errors
         *
         * @var bool
         */
        'display_errors' => true,

        /**
         * Site debug level
         *
         * @var string (message|debug)
         */
        'detail'         => 'message'
    ),

    /**
     * ------------------------------------------------------------------------
     * Proxy Settings
     * ------------------------------------------------------------------------
     */
    'proxy'             => array(
        'host'     => null,
        'port'     => null,
        'user'     => null,
        'password' => null
    ),

    /**
     * ------------------------------------------------------------------------
     * File upload settings
     * ------------------------------------------------------------------------
     */
    'upload'            => array(

        /**
         * Allowed file extensions
         *
         * @var string semi-colon separated.
         */
        'extensions' => '*.flv;*.jpg;*.gif;*.jpeg;*.ico;*.docx;*.xla;*.png;*.psd;*.swf;*.doc;*.txt;*.xls;*.xlsx;' .
            '*.csv;*.pdf;*.tiff;*.rtf;*.m4a;*.mov;*.wmv;*.mpeg;*.mpg;*.wav;*.3gp;*.avi;*.m4v;*.mp4;*.mp3;*.qt;*.ppt;' .
            '*.pptx;*.kml;*.xml;*.svg;*.webm;*.ogg;*.ogv'
    ),

    /**
     * ------------------------------------------------------------------------
     * Mail settings
     * ------------------------------------------------------------------------
     */
    'mail'              => array(
        'method'  => 'PHP_MAIL',
        'methods' => array(
            'smtp' => array(
                'server'     => '',
                'port'       => '',
                'username'   => '',
                'password'   => '',
                'encryption' => ''
            )
        )
    ),

    /**
     * ------------------------------------------------------------------------
     * Cache settings
     * ------------------------------------------------------------------------
     */
    'cache'             => array(

        /**
         * Enabled
         *
         * @var bool
         */
        'enabled'                  => true,

        /**
         * Lifetime
         *
         * @var int Seconds
         */
        'lifetime'                 => 21600,

        /**
         * Cache overrides
         *
         * @var bool
         */
        'overrides'                => true,

        /**
         * Cache Blocks
         *
         * @var bool
         */
        'blocks'                   => true,

        /**
         * Cache Assets
         *
         * @var bool
         */
        'assets'                   => false,

        /**
         * Cache Theme CSS/JS
         *
         * @var bool
         */
        'theme_css'                => true,

        /**
         * Cache full page
         *
         * @var bool|string (block|all)
         */
        'pages'                    => false,

        /**
         * Use Doctrine development mode
         *
         * @var bool
         */
        'doctrine_dev_mode'        => false,

        /**
         * How long to cache full page
         *
         * @var string
         */
        'full_page_lifetime'       => 'default',

        /**
         * Custom lifetime value, only used if concrete.cache.full_page_lifetime is 'custom'
         *
         * @var int
         */
        'full_page_lifetime_value' => null,


        'directory'   => DIR_FILES_UPLOADED_STANDARD . '/cache',
        'page'        => array(
            'directory' => DIR_FILES_UPLOADED_STANDARD . '/cache/pages',
            'adapter'      => 'file',
        ),
        'environment' => array(
            'file' => 'environment.cache'
        ),

        'levels' => array(
            'expensive' => array(
                'drivers' => array(
                    array(
                        'class' => '\Stash\Driver\Ephemeral',
                        'options' => array()
                    ),

                    array(
                        'class' => '\Stash\Driver\FileSystem',
                        'options' => array(
                            'path' => DIR_FILES_UPLOADED_STANDARD . '/cache',
                            'dirPermissions' => DIRECTORY_PERMISSIONS_MODE_COMPUTED,
                            'filePermissions' => FILE_PERMISSIONS_MODE_COMPUTED
                        )
                    ),
                )
            ),
            'object' => array(
                'drivers' => array(
                    array(
                        'class' => '\Stash\Driver\Ephemeral',
                        'options' => array()
                    )
                )
            )
        )

    ),

    'multilingual' =>   array(
        'enabled' => false, // note this will automatically be set to true if needed
        'redirect_home_to_default_locale' => false,
        'use_browser_detected_locale' => false,
        'default_locale' => false,
        'default_source_locale' => 'en_US'
    ),

    'design'            => array(
        'enable_custom' => true,
        'enable_layouts' => true
    ),

    /**
     * ------------------------------------------------------------------------
     * Logging settings
     * ------------------------------------------------------------------------
     */
    'log'               => array(

        /**
         * Log emails
         *
         * @var bool
         */
        'emails' => true,

        /**
         * Log Errors
         *
         * @var bool
         */
        'errors' => true,

        /**
         * Log Spam
         *
         * @var bool
         */
        'spam'   => false,


        'queries' => array(

            /**
             * Whether to log database queries or not.
             *
             * @var bool
             */
            'log' => false,


            'clear_on_reload' => false



        )
    ),
    'jobs'              => array(

        'enable_scheduling' => true

    ),

    'filesystem'        => array(

        'permissions'   => array(
            'file' => FILE_PERMISSIONS_MODE_COMPUTED,
            'directory' => DIRECTORY_PERMISSIONS_MODE_COMPUTED
        )
    ),

    'editor' => array(
        'concrete' => array(
            'enable_filemanager' => true,
            'enable_sitemap' => true
        ),
        'plugins' => array(
            'selected' => array(
                'concrete5lightbox',
                'undoredo',
                'specialcharacters',
                'table'
            )
        )
    ),

/**
     * ------------------------------------------------------------------------
     * Email settings
     * ------------------------------------------------------------------------
     */
    'email'             => array(

        /**
         * Enable emails
         *
         * @var bool
         */
        'enabled' => true,
        'default' => array(
            'address' => 'concrete5-noreply@' . $_SERVER['SERVER_NAME'],
            'name'    => ''
        ),
        'form_block' => array(
            'address' => false
        )
    ),

    /**
     * ------------------------------------------------------------------------
     * Marketplace settings
     * ------------------------------------------------------------------------
     */
    'marketplace'       => array(
        /**
         * Enable marketplace integration
         *
         * @var bool
         */
        'enabled'            => true,

        /**
         * Marketplace Token
         *
         * @var null|string
         */
        'token'              => null,

        /**
         * Marketplace Site url Token
         *
         * @var null|string
         */
        'site_token'         => null,

        /**
         * Enable intelligent search integration
         */
        'intelligent_search' => true,

        /**
         * Log requests
         */
        'log_requests' => false
    ),

    /**
     * ------------------------------------------------------------------------
     * Getting external news and help from concrete5.org
     * ------------------------------------------------------------------------
     */
    'external'              => array(

        /**
         * Provide help within the intelligent search
         *
         * @var bool concrete.external.intelligent_search_help
         */
        'intelligent_search_help' => true,

        /**
         * Display an overlay with up-to-date news from concrete5
         *
         * @var bool concrete.external.news_overlay
         */
        'news_overlay'            => true,

        /**
         * Enable concrete5 news within your site
         *
         * @var bool concrete.external.news
         */
        'news'                    => true,
    ),

    /**
     * --------------------------------------------------------------------
     * Miscellaneous settings
     * --------------------------------------------------------------------
     */
    'misc'              => array(
        'user_timezones'                => false,
        'package_backup_directory'      => DIR_FILES_UPLOADED_STANDARD . '/trash',
        'enable_progressive_page_reindex'      => true,
        'mobile_theme_id'               => 0,
        'sitemap_approve_immediately'   => true,
        'enable_translate_locale_en_us' => false,
        'page_search_index_lifetime'    => 259200,
        'enable_trash_can'              => true,
        'app_version_display_in_header' => true,
        'default_jpeg_image_compression'     => 80
    ),

    'theme' => array(

        'compress_preprocessor_output' => true
    ),

    'updates' => array(

        'enable_auto_update_core'       => false,
        'enable_auto_update_packages'   => false,
        'enable_permissions_protection' => true,
        'check_threshold' => 172800,
        'services' => array(
            'get_available_updates' => 'http://www.concrete5.org/tools/update_core',
            'inspect_update' => 'http://www.concrete5.org/tools/inspect_update'
        )
    ),
    'paths'             => array(
        'trash'  => '/!trash',
        'drafts' => '/!drafts'
    ),
    'conversations'     => array(
        'attachments_pending_file_set' => 'Conversation Messages (Pending)',
        'attachments_file_set'         => 'Conversation Messages',
        'attachments_enabled'          => true
    ),
    'icons'             => array(
        'page_template'        => array(
            'width'  => 120,
            'height' => 90
        ),
        'theme_thumbnail'      => array(
            'width'  => 120,
            'height' => 90
        ),
        'file_manager_listing' => array(
            'handle' => 'file_manager_listing',
            'width'  => 60,
            'height' => 60
        ),
        'file_manager_detail'  => array(
            'handle' => 'file_manager_detail',
            'width'  => 400
        ),
        'user_avatar'          => array(
            'width'   => 80,
            'height'  => 80,
            'default' => ASSETS_URL_IMAGES . '/avatar_none.png'
        )
    ),
    'sitemap_xml'       => array(
        'file'      => 'sitemap.xml',
        'frequency' => 'weekly',
        'priority'  => 0.5
    ),

    /**
     * ------------------------------------------------------------------------
     * Accessibility
     * ------------------------------------------------------------------------
     */
    'accessibility'     => array(
        /**
         * Show titles in the concrete5 toolbars
         *
         * @var bool
         */
        'toolbar_titles'     => false,

        /**
         * Increase the font size in the concrete5 toolbars
         *
         * @var bool
         */
        'toolbar_large_font' => false,

        /**
         * Show help system
         *
         * @var bool
         */
        'display_help_system' => true
    ),

    /**
     * ------------------------------------------------------------------------
     * Internationalization
     * ------------------------------------------------------------------------
     */
    'i18n'              => array(

        /**
         * Enable internationalization
         */
        'enabled'               => true,

        /**
         * Allow users to choose language on login
         *
         * @var bool
         */
        'choose_language_login' => false

    ),
    'urls'              => array(
        'concrete5'              => 'http://www.concrete5.org',
        'concrete5_secure'       => 'https://www.concrete5.org',
        'newsflow'               => 'http://newsflow.concrete5.org',
        'background_feed'        => '//backgroundimages.concrete5.org/wallpaper',
        'background_feed_secure' => 'https://backgroundimages.concrete5.org/wallpaper',
        'background_info'        => 'http://backgroundimages.concrete5.org/get_image_data.php',
        'help'                   => array(
            'developer'          => 'http://www.concrete5.org/documentation/developers/5.7/',
            'user'          => 'http://www.concrete5.org/documentation/using-concrete5-7',
            'forum'          => 'http://www.concrete5.org/community/forums'
        ),
        'paths'                  => array(
            'menu_help_service' => '/tools/get_remote_help_list/',
            'theme_preview'     => '/tools/preview_theme/',
            'site_page'         => '/private/sites',
            'newsflow_slot_content'      => '/tools/slot_content/',
            'marketplace'       => array(
                'connect'           => '/marketplace/connect',
                'connect_success'   => '/marketplace/connect/-/connected',
                'connect_validate'  => '/marketplace/connect/-/validate',
                'connect_new_token' => '/marketplace/connect/-/generate_token',
                'checkout'          => '/cart/-/add/',
                'purchases'         => '/marketplace/connect/-/get_available_licenses',
                'item_information'  => '/marketplace/connect/-/get_item_information',
                'item_free_license' => '/marketplace/connect/-/enable_free_license',
                'remote_item_list'  => '/marketplace/'
            )
        )
    ),

    /**
     * ------------------------------------------------------------------------
     * White labeling.
     * ------------------------------------------------------------------------
     */
    'white_label'       => array(

        /**
         * Custom Logo source path relative to the public directory.
         *
         * @var bool|string The logo path
         */
        'logo'                 => false,

        /**
         * Custom Name
         *
         * @var bool|string The name
         */
        'name'                 => false,

        /**
         * Dashboard background image url
         *
         * @var null|string
         */
        'dashboard_background' => null
    ),
    'session'           => array(

        'name'         => 'CONCRETE5',
        'handler'      => 'file',
        'max_lifetime' => 7200,
        'cookie'       => array(
            'cookie_path'     => false, // set a specific path here if you know it, otherwise it'll default to relative
            'cookie_lifetime' => 0,
            'cookie_domain'   => false,
            'cookie_secure'   => false,
            'cookie_httponly' => false
        )
    ),

    /**
     * ------------------------------------------------------------------------
     * User information and registration settings.
     * ------------------------------------------------------------------------
     */
    'user'              => array(
        /**
         * --------------------------------------------------------------------
         * Registration settings.
         * --------------------------------------------------------------------
         */
        'registration'      => array(

            /**
             * Registration
             *
             * @var bool
             */
            'enabled'            => false,

            /**
             * Registration type
             *
             * @var string The type (disabled|enabled|validate_email|manual_approve)
             */
            'type'               => 'disabled',

            /**
             * Enable Registration Captcha
             *
             * @var bool
             */
            'captcha'            => true,

            /**
             * Use emails instead of usernames to log in
             *
             * @var bool
             */
            'email_registration' => false,

            /**
             * Validate emails during registration
             *
             * @var bool
             */
            'validate_email'     => false,

            /**
             * Admins approve each registration
             *
             * @var bool
             */
            'approval'           => false,

            /**
             * Send notifications after successful registration.
             *
             * @var bool|string Email to notify
             */
            'notification'       => false
        ),

        /**
         * --------------------------------------------------------------------
         * Gravatar Settings
         * --------------------------------------------------------------------
         */
        'gravatar'          => array(
            'enabled'   => false,
            'max_level' => 0,
            'image_set' => 0
        ),
        'group'             => array(

            'badge' => array(

                'default_point_value' => 50
            )

        ),

        /**
         * Enable public user profiles
         *
         * @var bool
         */
        'profiles_enabled'  => false,

        /**
         * Enable user timezones
         *
         * @var bool
         */
        'timezones_enabled' => false,
        'username'          => array(
            'maximum'      => 64,
            'minimum'      => 3,
            'allow_spaces' => false

        ),
        'password'          => array(
            'maximum'        => 128,
            'minimum'        => 5,
            'hash_portable'  => false,
            'hash_cost_log2' => 12
        ),
        'private_messages'  => array(
            'throttle_max'          => 20,
            'throttle_max_timespan' => 15 // minutes
        )

    ),

    /**
     * ------------------------------------------------------------------------
     * Spam
     * ------------------------------------------------------------------------
     */
    'spam'              => array(
        /**
         * Whitelist group ID
         *
         * @var int
         */
        'whitelist_group' => 0,

        /**
         * Notification email
         *
         * @var string
         */
        'notify_email'    => ''
    ),

    /**
     * ------------------------------------------------------------------------
     * Security
     * ------------------------------------------------------------------------
     */
    'security'          => array(
        'ban'   => array(
            'ip' => array(

                'enabled'  => true,

                /**
                 * Maximum attempts
                 */
                'attempts' => 5,

                /**
                 * Threshold time
                 */
                'time'     => 300,

                /**
                 * Ban length in minutes
                 */
                'length'   => 10
            )
        )
    ),

    /**
     * ------------------------------------------------------------------------
     * Permissions and behaviors toggles.
     * ------------------------------------------------------------------------
     */
    'permissions'       => array(
        /**
         * Forward to login if access is denied
         *
         * @var bool
         */
        'forward_to_login'              => true,

        /**
         * Permission model
         *
         * @var string The permission model (simple|advanced)
         */
        'model'                         => 'simple',

        /**
         * Use collection ID for page permission identifier
         *
         * @var bool
         */
        'page_permission_collection_id' => true
    ),

    /**
     * ------------------------------------------------------------------------
     * SEO Settings
     * ------------------------------------------------------------------------
     */
    'seo'               => array(

        'tracking'             => array(
            /**
             * User defined tracking code
             *
             * @var string
             */
            'code'          => '',

            /**
             * Tracking code position
             *
             * @var string (top|bottom)
             */
            'code_position' => 'bottom'

        ),
        'exclude_words'        => 'a, an, as, at, before, but, by, for, from, is, in, into, like, of, off, on, onto, per, ' .
            'since, than, the, this, that, to, up, via, with',

        /**
         * URL rewriting
         *
         * Doesn't impact URL_REWRITING_ALL which is set at a lower level and
         * controls whether ALL items will be rewritten.
         *
         * @var bool
         */
        'url_rewriting'           => false,
        'url_rewriting_all'       => false,
        'redirect_to_canonical_url'  => false,
        'canonical_url'          => null,
        'canonical_ssl_url'          => null,
        'trailing_slash'          => false,
        'title_format'            => '%1$s :: %2$s',
        'title_segment_separator' => ' :: ',
        'page_path_separator'     => '-',
        'group_name_separator'    => ' / ',
        'segment_max_length'      => 128,
        'paging_string'           => 'ccm_paging_p'
    ),

    /**
     * ------------------------------------------------------------------------
     * Statistics Settings
     * ------------------------------------------------------------------------
     */
    'statistics'        => array(
//        'track_page_views' => true
    ),
    'limits'            => array(
        'sitemap_pages'           => 100,
        'delete_pages'            => 10,
        'copy_pages'              => 10,
        'page_search_index_batch' => 200,
        'job_queue_batch'         => 10,
        'style_customizer' => array(
            'size_min' => -50,
            'size_max' => 200,
        )
    )
);
