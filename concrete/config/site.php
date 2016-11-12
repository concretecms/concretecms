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
                            'tabletools',
                            'toolbar',
                            'undo',
                            'wysiwygarea'
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

        ],

    ],
];
