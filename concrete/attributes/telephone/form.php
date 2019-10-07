<?php
defined('C5_EXECUTE') or die('Access Denied.');
echo $form->telephone(
    $this->field('value'),
    $value,
    [
        'placeholder' => h($akTelephonePlaceholder),
    ]
);
