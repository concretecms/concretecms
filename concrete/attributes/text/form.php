<?php
defined('C5_EXECUTE') or die("Access Denied.");
print $form->text(
    $this->field('value'),
    $value,
    [
        'placeholder' => tc('AttributeKeyPlaceholder', h($akTextPlaceholder))
    ]
);
