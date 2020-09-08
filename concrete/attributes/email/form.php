<?php
defined('C5_EXECUTE') or die("Access Denied.");
print $form->email(
    $this->field('value'),
    $value,
    [
        'placeholder' => tc('AttributeKeyPlaceholder', h($akTextPlaceholder))
    ]
);
