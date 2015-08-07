<?php


return array(
    'extensions' => array(

        ##
        # Controls
        ##
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
