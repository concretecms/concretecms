<?php

return [

    'default' => 'default',

    'sites' => [

        'default' => [

            'handle' => 'default',

            'name' => 'concrete5',

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

            'editor' => [
                'concrete' => [
                    'enable_filemanager' => true,
                    'enable_sitemap' => true,
                ],
                'ckeditor4' => [
                    'plugins' => [
                        'selected' => [
                            'autogrow',
                            'a11yhelp',
                            'basicstyles',
                            'colordialog',
                            'contextmenu',
                            'concrete5link',
                            'concrete5styles',
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
                            'wysiwygarea'
                        ],
                        'selected_hidden' => [
                            'concrete5filemanager',
                            'concrete5inline',
                            'concrete5uploadimage',
                            'dialogadvtab',
                            'divarea',
                            'floatingspace',
                            'normalizeonchange',
                            'resize',
                            'toolbar',
                            'wysiwygarea',
                        ]
                    ]
                ],
                'plugins' => [
                    'selected' => [
                        'concrete5lightbox',
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
            ],

        ],

    ],
];
