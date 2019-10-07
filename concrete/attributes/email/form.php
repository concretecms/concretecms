<?php
defined('C5_EXECUTE') or die('Access Denied.');
echo $form->email(
    $this->field('value'),
    $value,
    [
        'placeholder' => h($akEmailPlaceholder),
    ]
);
