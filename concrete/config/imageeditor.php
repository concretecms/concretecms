<?php


return array(
    'extensions' => array(

        ##
        # Controls
        ##

        /**
         * Extension that provides image placement and other default core functionality
         *
         * @var array imageeditor.extensions.core/position
         */
        'core/position' => array(
            'type' => Concrete\Core\ImageEditor\ImageEditor::ImageEditorExtensionControl,
            'name' => tc('ImageEditorControlSetName', 'Position'),
            'handle' => 'position',
            'src' => 'core/imageeditor/control/position',
            'view' => 'image-editor/controls/position',
            'assets' => array(
                'core/imageeditor/control/position' => array('css')
            )
        ),

        /**
         * Extension for adding filter management and slideout
         *
         * @var array imageeditor.extensions.core/filter
         */
        'core/filter' => array(
            'type' => Concrete\Core\ImageEditor\ImageEditor::ImageEditorExtensionControl,
            'name' => tc('ImageEditorControlSetName', 'Filter'),
            'handle' => 'filter',
            'src' => 'core/imageeditor/control/filter',
            'view' => 'image-editor/controls/filter',
            'assets' => array(
                'core/imageeditor/control/filter' => array('css')
            )
        ),

        ##
        # Filters
        ##

        /**
         * Gaussian blur filter
         *
         * @var array imageeditor.extensions.core/filter/gaussian_blur
         */
        'core/filter/gaussian_blur' => array(
            'type' => Concrete\Core\ImageEditor\ImageEditor::ImageEditorExtensionFilter,
            'name' => tc('ImageEditorFilterName', 'Gaussian Blur'),
            'handle' => 'gaussian_blur',
            'src' => 'core/imageeditor/filter/gaussian_blur',
            'view' => 'image-editor/filters/gaussian_blur',
            'assets' => array(
                'core/imageeditor/filter/gaussian_blur' => array('css')
            )
        ),

        /**
         * Grayscale filter
         *
         * @var array imageeditor.extensions.core/filter/grayscale
         */
        'core/filter/grayscale' => array(
            'type' => Concrete\Core\ImageEditor\ImageEditor::ImageEditorExtensionFilter,
            'name' => tc('ImageEditorFilterName', 'Grayscale'),
            'handle' => 'grayscale',
            'src' => 'core/imageeditor/filter/grayscale',
            'view' => 'image-editor/filters/grayscale',
            'assets' => array(
                'core/imageeditor/filter/grayscale' => array('css')
            )
        ),

        /**
         * No filter
         *
         * @var array imageeditor.extensions.core/filter/none
         */
        'core/filter/none' => array(
            'type' => Concrete\Core\ImageEditor\ImageEditor::ImageEditorExtensionFilter,
            'name' => tc('ImageEditorFilterName', 'None'),
            'handle' => 'none',
            'src' => 'core/imageeditor/filter/none',
            'view' => 'image-editor/filters/none',
            'assets' => array(
                'core/imageeditor/filter/none' => array('css')
            )
        ),

        /**
         * Sepia filter
         *
         * @var array imageeditor.extensions.core/filter/sepia
         */
        'core/filter/sepia' => array(
            'type' => Concrete\Core\ImageEditor\ImageEditor::ImageEditorExtensionFilter,
            'name' => tc('ImageEditorFilterName', 'Sepia'),
            'handle' => 'sepia',
            'src' => 'core/imageeditor/filter/sepia',
            'view' => 'image-editor/filters/sepia',
            'assets' => array(
                'core/imageeditor/filter/sepia' => array('css')
            )
        ),

        /**
         * Vignette filter
         *
         * @var array imageeditor.extensions.core/filter/vignette
         */
        'core/filter/vignette' => array(

            'type' => Concrete\Core\ImageEditor\ImageEditor::ImageEditorExtensionFilter,
            'name' => tc('ImageEditorFilterName', 'Vignette'),
            'handle' => 'vignette',
            'src' => 'core/imageeditor/filter/vignette',
            'view' => 'image-editor/filters/vignette',
            'assets' => array(
                'core/imageeditor/filter/vignette' => array('css')
            )
        ),
    )
);
