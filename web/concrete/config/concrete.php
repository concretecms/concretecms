<?php

return array(

    /**
     * Current Version
     *
     * @var string
     */
    'version'             => '5.7.0b2',

    /**
     * Installation status
     *
     * @var bool
     */
    'installed'           => true,

    /**
     * The current Site Name
     *
     * @var string concrete.core.site
     */
    'site' => 'concrete5',

    /**
     * The current Locale
     */
    'locale' => 'en_US',

    /**
     * Maintenance mode
     */
    'maintenance_mode' => false,

    /**
     * ------------------------------------------------------------------------
     * Debug settings
     * ------------------------------------------------------------------------
     */
    'debug' => array(

        /**
         * Site debug level
         * @var int
         */
        'level' => 1

    ),
    /**
     * ------------------------------------------------------------------------
     * Proxy Settings
     * ------------------------------------------------------------------------
     */
    'proxy' => array(
        'host' => null,
        'port' => null,
        'user' => null,
        'password' => null
    ),

    /**
     * ------------------------------------------------------------------------
     * File upload settings
     * ------------------------------------------------------------------------
     */
    'upload' => array(

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
    'mail' => array(
        'method' => 'smtp',
        'methods' => array(
            'smtp' => array(
                'server' => '',
                'port' => '',
                'username' => '',
                'password' => '',
                'encryption' => ''
            )
        )
    ),

    /**
     * ------------------------------------------------------------------------
     * Cache settings
     * ------------------------------------------------------------------------
     */
    'cache' => array(

        /**
         * Lifetime
         *
         * @var int Seconds
         */
        'lifetime' => 21600,

        /**
         * Cache overrides
         *
         * @var bool
         */
        'overrides' => false,

        /**
         * Cache Blocks
         *
         * @var bool
         */
        'blocks' => false,

        /**
         * Cache Assets
         *
         * @var bool
         */
        'assets' => false,

        /**
         * Cache Theme CSS/JS
         *
         * @var bool
         */
        'theme_css' => false,

        /**
         * Cache full page
         *
         * @var bool|string (block|all)
         */
        'pages' => false,

        /**
         * How long to cache full page
         *
         * @var string
         */
        'full_page_lifetime' => 'default',

        /**
         * Custom lifetime value, only used if concrete.cache.full_page_lifetime is 'custom'
         *
         * @var int
         */
        'full_page_lifetime_value' => null
    ),

    /**
     * ------------------------------------------------------------------------
     * Logging settings
     * ------------------------------------------------------------------------
     */
    'log' => array(

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
        'spam' => false
    ),

    /**
     * ------------------------------------------------------------------------
     * Email settings
     * ------------------------------------------------------------------------
     */
    'email' => array(

        /**
         * Enable emails
         *
         * @var bool
         */
        'enabled' => true
    ),

    /**
     * ------------------------------------------------------------------------
     * Marketplace settings
     * ------------------------------------------------------------------------
     */
    'marketplace' => array(
        /**
         * Enable marketplace integration
         *
         * @var bool
         */
        'enabled' => true,

        /**
         * Marketplace Token
         *
         * @var null|string
         */
        'token' => null,

        /**
         * Marketplace Site url Token
         *
         * @var null|string
         */
        'site_token' => null,

        /**
         * Enable intelligent search integration
         */
        'intelligent_search' => false
    ),

    /**
     * ------------------------------------------------------------------------
     * Various core settings
     * ------------------------------------------------------------------------
     */
    'core' => array(

        /**
         * Provide help within the intelligent search
         *
         * @var bool concrete.core.intelligent_search_help
         */
        'intelligent_search_help' => true,

        /**
         * Display an overlay with up-to-date news from concrete5
         *
         * @var bool concrete.core.news_overlay
         */
        'news_overlay' => true,

        /**
         * Enable concrete5 news within your site
         *
         * @var bool concrete.core.news
         */
        'news' => true,
    ),

    /**
     * --------------------------------------------------------------------
     * Miscellaneous settings
     * --------------------------------------------------------------------
     */
    'misc' => array(
        'user_timezones' => false,
        'progressive_page_reindex' => true,
        'mail_send_method' => 'PHP_MAIL',
        'mobile_theme_id' => 0,
        'seen_introduction' => false
    ),

    /**
     * ------------------------------------------------------------------------
     * Accessibility
     * ------------------------------------------------------------------------
     */
    'accessibility' => array(
        /**
         * Show titles in the concrete5 toolbars
         *
         * @var bool
         */
        'toolbar_titles' => false,

        /**
         * Increase the font size in the concrete5 toolbars
         *
         * @var bool
         */
        'toolbar_large_font' => false
    ),

    /**
     * ------------------------------------------------------------------------
     * Internationalization
     * ------------------------------------------------------------------------
     */
    'i18n' => array(

        /**
         * Enable internationalization
         */
        'enabled' => true,

        /**
         * Allow users to choose language on login
         *
         * @var bool
         */
        'choose_language_login' => false

    ),

    /**
     * ------------------------------------------------------------------------
     * White labeling.
     * ------------------------------------------------------------------------
     */
    'white_label' => array(

        /**
         * Custom Logo source path relative to the public directory.
         *
         * @var bool|string The logo path
         */
        'logo' => false,

        /**
         * Custom Name
         *
         * @var bool|string The name
         */
        'name' => false

    ),

    /**
     * ------------------------------------------------------------------------
     * User information and registration settings.
     * ------------------------------------------------------------------------
     */
    'user' => array(
        /**
         * --------------------------------------------------------------------
         * Registration settings.
         * --------------------------------------------------------------------
         */
        'registration' => array(

            /**
             * Registration
             *
             * @var bool
             */
            'enabled' => false,

            /**
             * Registration type
             *
             * @var string The type (disabled|enabled|validate_email|manual_approve)
             */
            'type' => 'disabled',

            /**
             * Enable Registration Captcha
             *
             * @var bool
             */
            'captcha' => true,

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
            'validate_email' => false,

            /**
             * Admins approve each registration
             *
             * @var bool
             */
            'approval' => false,

            /**
             * Send notifications after successful registration.
             *
             * @var bool|string Email to notify
             */
            'notification' => false
        ),
        /**
         * --------------------------------------------------------------------
         * Gravatar Settings
         * --------------------------------------------------------------------
         */
        'gravatar' => array(
            'enabled' => false,
            'max_level' => 0,
            'image_set' => 0
        ),

        /**
         * Enable public user profiles
         *
         * @var bool
         */
        'profiles_enabled' => false,

        /**
         * Enable user timezones
         *
         * @var bool
         */
        'timezones_enabled' => false
    ),

    /**
     * ------------------------------------------------------------------------
     * Spam
     * ------------------------------------------------------------------------
     */
    'spam' => array(
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
        'notify_email' => ''
    ),

    /**
     * ------------------------------------------------------------------------
     * Security
     * ------------------------------------------------------------------------
     */
    'security' => array(
        'ban' => array(
            'ip' => array(

                'enabled' => true,

                /**
                 * Maximum attempts
                 */
                'attempts' => 5,

                /**
                 * Threshold time
                 */
                'time' => 300,

                /**
                 * Ban length in minutes
                 */
                'length' => 10
            )
        ),
        'token' => array(
            'encryption' => '',
            'jobs' => '',
            'validation' => ''
        )
    ),

    /**
     * ------------------------------------------------------------------------
     * Permissions and behaviors toggles.
     * ------------------------------------------------------------------------
     */
    'permissions' => array(
        /**
         * Forward to login if access is denied
         *
         * @var bool
         */
        'forward_to_login' => true,

        /**
         * Permission model
         *
         * @var string The permission model (simple|advanced)
         */
        'model' => 'simple',

        /**
         * Allow area/layout specific permissions in edit mode
         *
         * @var bool
         */
        'enable_area' => true,

        /**
         * Allow custom design
         *
         * @var bool
         */
        'enable_custom_design' => true,

        /**
         * Use collection ID for page permission identifier
         *
         * @var bool
         */
        'page_permission_collection_id' => \Concrete\Core\Permission\Access\PageAccess::usePermissionCollectionIDForIdentifier()
    ),

    /**
     * ------------------------------------------------------------------------
     * SEO Settings
     * ------------------------------------------------------------------------
     */
    'seo' => array (

        'tracking' => array(
            /**
             * User defined tracking code
             *
             * @var string
             */
            'code' => '',

            /**
             * Tracking code position
             *
             * @var string (top|bottom)
             */
            'code_position' => 'bottom'
        ),

        'exclude_words' => 'a, an, as, at, before, but, by, for, from, is, in, into, like, of, off, on, onto, per, ' .
            'since, than, the, this, that, to, up, via, with',

        /**
         * URL rewriting
         *
         * Doesn't impact URL_REWRITING_ALL which is set at a lower level and
         * controls whether ALL items will be rewritten.
         *
         * @var bool
         */
        'url_rewriting' => false,

    ),

    /**
     * ------------------------------------------------------------------------
     * Statistics Settings
     * ------------------------------------------------------------------------
     */
    'statistics' => array(
        'track_page_views' => true
    )
);
