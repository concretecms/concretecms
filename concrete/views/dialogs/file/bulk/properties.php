<?php
defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\Dialog\File\Bulk\Properties $controller
 * @var Concrete\Core\Filesystem\Element $keySelector
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Entity\File\File[] $files
 */
?>

<form method="post" action="<?=$controller->action('submit')?>" data-dialog-form="files-attributes">
    <?php
    foreach ($files as $file) {
        echo $form->hidden("fID{$file->getFileID()}", $file->getFileID(), ['name' => 'fID[]']);
    }
    ?>

    <div class="ccm-ui">
        <?php
            $keySelector->render();
        ?>
    </div>

    <div class="dialog-buttons">
        <button class="btn btn-secondary" data-dialog-action="cancel"><?=t('Cancel')?></button>
        <button type="button" data-dialog-action="submit" class="btn btn-primary"><?=t('Save')?></button>
    </div>

</form>
