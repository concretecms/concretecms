<?php

// switch display type here
if ($akTextareaDisplayMode == 'text' || $akTextareaDisplayMode == '') { ?>

    <?php
    echo $form->textarea(
        $view->controller->field('value'),
        h($value),
        [
            'rows' => 5,
            'placeholder' => tc('AttributeKeyPlaceholder', h($akTextPlaceholder))
        ]
    );
    ?>

<?php } else {
    echo Core::make('editor')->outputStandardEditor(
        $view->controller->field('value'),
        h($value)
    );
}
