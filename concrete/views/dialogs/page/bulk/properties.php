<?php
defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\Dialog\Page\Bulk\Properties $controller
 * @var Concrete\Core\Filesystem\Element $keySelector
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Page\Page[] $pages
 */
?>

<form method="post" action="<?=$controller->action('submit')?>" data-dialog-form="pages-attributes">
    <?php
        foreach ($pages as $page) {
            echo $form->hidden("item{$page->getCollectionID()}", $page->getCollectionID(), ['name' => 'item[]']);
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

