<?php


return [
    'extensions' => [

        //#
        // Controls
        //#

        /*
         * Extension that provides image placement and other default core functionality
         *
         * @var array imageeditor.extensions.core/position
         */
        'core/position' => [
            'type' => Concrete\Core\ImageEditor\ImageEditor::ImageEditorExtensionControl,
            'name' => tc('ImageEditorControlSetName', 'Position'),
            'handle' => 'position',
            'src' => 'core/imageeditor/control/position',
            'view' => 'image-editor/controls/position',
            'assets' => [
                'core/imageeditor/control/position' => ['css'],
            ],
        ],

        /*
         * Extension that provides image placement and other default core functionality
         *
         * @var array imageeditor.extensions.core/colors
         */
        'core/colors' => [
            'type' => Concrete\Core\ImageEditor\ImageEditor::ImageEditorExtensionControl,
            'name' => tc('ImageEditorControlSetName', 'Colors'),
            'handle' => 'colors',
            'src' => 'core/imageeditor/control/colors',
            'view' => 'image-editor/controls/colors',
        ],

        /*
         * Extension for adding filter management and slideout
         *
         * @var array imageeditor.extensions.core/filter
         */
        'core/filter' => [
            'type' => Concrete\Core\ImageEditor\ImageEditor::ImageEditorExtensionControl,
            'name' => tc('ImageEditorControlSetName', 'Filter'),
            'handle' => 'filter',
            'src' => 'core/imageeditor/control/filter',
            'view' => 'image-editor/controls/filter',
            'assets' => [
                'core/imageeditor/control/filter' => ['css'],
            ],
        ],

        //#
        // Filters
        //#

        /*
         * Gaussian blur filter
         *
         * @var array imageeditor.extensions.core/filter/gaussian_blur
         */
        'core/filter/gaussian_blur' => [
            'type' => Concrete\Core\ImageEditor\ImageEditor::ImageEditorExtensionFilter,
            'name' => tc('ImageEditorFilterName', 'Gaussian Blur'),
            'handle' => 'gaussian_blur',
            'src' => 'core/imageeditor/filter/gaussian_blur',
            'view' => 'image-editor/filters/gaussian_blur',
            'assets' => [
                'core/imageeditor/filter/gaussian_blur' => ['css'],
            ],
        ],

        /*
         * Grayscale filter
         *
         * @var array imageeditor.extensions.core/filter/grayscale
         */
        'core/filter/grayscale' => [
            'type' => Concrete\Core\ImageEditor\ImageEditor::ImageEditorExtensionFilter,
            'name' => tc('ImageEditorFilterName', 'Grayscale'),
            'handle' => 'grayscale',
            'src' => 'core/imageeditor/filter/grayscale',
            'view' => 'image-editor/filters/grayscale',
            'assets' => [
                'core/imageeditor/filter/grayscale' => ['css'],
            ],
        ],

        /*
         * No filter
         *
         * @var array imageeditor.extensions.core/filter/none
         */
        'core/filter/none' => [
            'type' => Concrete\Core\ImageEditor\ImageEditor::ImageEditorExtensionFilter,
            'name' => tc('ImageEditorFilterName', 'None'),
            'handle' => 'none',
            'src' => 'core/imageeditor/filter/none',
            'view' => 'image-editor/filters/none',
            'assets' => [
                'core/imageeditor/filter/none' => ['css'],
            ],
        ],

        /*
         * Sepia filter
         *
         * @var array imageeditor.extensions.core/filter/sepia
         */
        'core/filter/sepia' => [
            'type' => Concrete\Core\ImageEditor\ImageEditor::ImageEditorExtensionFilter,
            'name' => tc('ImageEditorFilterName', 'Sepia'),
            'handle' => 'sepia',
            'src' => 'core/imageeditor/filter/sepia',
            'view' => 'image-editor/filters/sepia',
            'assets' => [
                'core/imageeditor/filter/sepia' => ['css'],
            ],
        ],

        /*
         * Vignette filter
         *
         * @var array imageeditor.extensions.core/filter/vignette
         */
        'core/filter/vignette' => [

            'type' => Concrete\Core\ImageEditor\ImageEditor::ImageEditorExtensionFilter,
            'name' => tc('ImageEditorFilterName', 'Vignette'),
            'handle' => 'vignette',
            'src' => 'core/imageeditor/filter/vignette',
            'view' => 'image-editor/filters/vignette',
            'assets' => [
                'core/imageeditor/filter/vignette' => ['css'],
            ],
        ],
    ],
];
