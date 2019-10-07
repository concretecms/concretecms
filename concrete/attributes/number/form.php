<?php
defined('C5_EXECUTE') or die('Access Denied.');
echo $form->number(
    $this->field('value'),
    $value,
    [
        'placeholder' => h($akNumberPlaceholder),
    ],
    [
        'step' => 'any',
    ]
);
