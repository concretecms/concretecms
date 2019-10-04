<?php
defined('C5_EXECUTE') or die('Access Denied.');
echo $form->url(
    $this->field('value'),
    $value,
    [
        'placeholder' => h($akUrlPlaceholder),
    ]
);
