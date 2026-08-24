<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var \Concrete\Core\Form\Service\Form $form
 * @var array<string,string> $availableTypes
 * @var string[] $types
 */

?>
<div class="form-group">
    <?= $form->label('types', t('Types to Display')) ?>
    <?php
    foreach ($availableTypes as $typeHandle => $typeName) {
        ?>
        <div class="form-check">
            <?= $form->checkbox('types[]', $typeHandle, in_array($typeHandle, $types, true)) ?>
            <?= $form->label('types_' . $typeHandle, $typeName, ['class' => 'form-check-label']) ?>
        </div>
        <?php
    }
    ?>
</div>
