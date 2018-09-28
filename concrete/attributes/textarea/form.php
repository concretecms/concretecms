<?php

// switch display type here
if ($akTextareaDisplayMode == 'text' || $akTextareaDisplayMode == '') { ?>

    <?php
    echo $form->textarea(
        $view->controller->field('value'),
        h($value),
        array('rows' => 5)
    );
    ?>

<?php } else {
    echo Core::make('editor')->outputStandardEditor(
        $view->controller->field('value'),
        h($value)
    );
}
