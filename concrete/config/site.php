<?php

return [
    'default' => 'default',
    'sites' => [
        'default' => [
            'handle' => 'default',
            'name' => 'Concrete',
            'user' => [
                'profiles_enabled' => false,
                'gravatar' => [
                    'enabled' => false,
                    'max_level' => 0,
                    'image_set' => 0,
                ],
                /*
                 * Show the account menu in page footer when users are not logged in.
                 * Can be overridden in site themes by setting $display_account_menu when using the footer_required element.
                 *
                 * @var bool
                 */
                'display_account_menu' => true,
            ],
            'misc' => [
                // File ID for favicon
                'favicon_fid' => null,
                // File ID for iPhone home screen icon
                'iphone_home_screen_thumbnail_fid' => null,
                // File ID for Windows 8 tile icon
                'modern_tile_thumbnail_fid' => null,
                // Background color for Windows 8 tile icon
                'modern_tile_thumbnail_bgcolor' => null,
                // theme-color meta-tag (eg color of toolbar for Chrome 39+ on Android)
                'browser_toolbar_color' => null,
                // Default value for the forceAbsoluteURL option of Concrete\Core\Html\Image
                'img_src_absolute' => false,
            ],
            'editor' => [
                'concrete' => [
                    'enable_filemanager' => true,
                    'enable_sitemap' => true,
                ],
                'ckeditor4' => [
                    'custom_config_options' => '',
                    'editor_function_options' => '',
                    'plugins' => [
                        'selected_default' => [
                            'autogrow',
                            'a11yhelp',
                            'basicstyles',
                            'colordialog',
                            'contextmenu',
                            'concretelink',
                            'concretestyles',
                            'dialogadvtab',
                            'divarea',
                            'elementspath',
                            'enterkey',
                            'entities',
                            'floatingspace',
                            'format',
                            'htmlwriter',
                            'image',
                            'indentblock',
                            'indentlist',
                            'justify',
                            'link',
                            'list',
                            'liststyle',
                            'magicline',
                            'removeformat',
                            'resize',
                            'showblocks',
                            'showborders',
                            'sourcearea',
                            'sourcedialog',
                            'stylescombo',
                            'tab',
                            'table',
                            'tableresize',
                            'tableselection',
                            'tabletools',
                            'toolbar',
                            'undo',
                            'wysiwygarea',
                        ],
                        'selected_hidden' => [
                            'concretefilemanager',
                            'concreteinline',
                            'concreteuploadimage',
                            'dialogadvtab',
                            'divarea',
                            'floatingspace',
                            'normalizeonchange',
                            'resize',
                            'toolbar',
                            'wysiwygarea',
                        ],
                    ],
                    'toolbar_groups' => [
                        ['name' => 'mode', 'groups' => ['mode']],
                        ['name' => 'document', 'groups' => ['document']],
                        ['name' => 'doctools', 'groups' => ['doctools']],
                        ['name' => 'clipboard', 'groups' => ['clipboard']],
                        ['name' => 'undo', 'groups' => ['undo']],
                        ['name' => 'find', 'groups' => ['find']],
                        ['name' => 'selection', 'groups' => ['selection']],
                        ['name' => 'spellchecker', 'groups' => ['spellchecker']],
                        ['name' => 'editing', 'groups' => ['editing']],
                        ['name' => 'basicstyles', 'groups' => ['basicstyles']],
                        ['name' => 'cleanup', 'groups' => ['cleanup']],
                        ['name' => 'list', 'groups' => ['list']],
                        ['name' => 'indent', 'groups' => ['indent']],
                        ['name' => 'blocks', 'groups' => ['blocks']],
                        ['name' => 'align', 'groups' => ['align']],
                        ['name' => 'bidi', 'groups' => ['bidi']],
                        ['name' => 'paragraph', 'groups' => ['paragraph']],
                        ['name' => 'links', 'groups' => ['links']],
                        ['name' => 'insert', 'groups' => ['insert']],
                        ['name' => 'forms', 'groups' => ['forms']],
                        ['name' => 'styles', 'groups' => ['styles']],
                        ['name' => 'colors', 'groups' => ['colors']],
                        ['name' => 'tools', 'groups' => ['tools']],
                        ['name' => 'others', 'groups' => ['others']],
                        ['name' => 'about', 'groups' => ['about']],
                    ],
                ],
                'plugins' => [
                    'selected' => [
                        'concretelightbox',
                        'undoredo',
                        'specialcharacters',
                        'table',
                    ],
                ],
            ],
            'multilingual' => [
                'redirect_home_to_default_locale' => false,
                'use_browser_detected_locale' => false,
                'default_source_locale' => 'en_US',
                'always_track_user_locale' => true,
            ],
            'seo' => [
                'canonical_tag' => [
                    // Add a <meta rel="canonical" href="..."> tag to pages?
                    'enabled' => true,
                    // List of querystring parameters to be included in SEO canonical URLs
                    'included_querystring_parameters' => [
                        'ccm_paging_p',
                    ],
                ],
                'tracking' => [
                    'code' => [
                        'header' => '',
                        'footer' => '',
                    ],
                ],
            ],
        ],
    ],
];
