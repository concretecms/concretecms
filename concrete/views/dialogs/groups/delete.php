<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\Dialog\Groups\Delete $controller
 * @var Concrete\Core\View\DialogView $view
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\User\Group\Group|null $group
 */

if ($group === null) {
    ?>
    <div class="alert-message info">
        <?= t('Unable to find the requested group') ?>
    </div>
    <?php
    return;
}
?>

<?= t('Are you sure you would like to delete the following group?') ?><br />
<div class="alert alert-info">
    <?= $group->getGroupDisplayName() ?>
</div>
<form method="POST" data-dialog-form="delete-groups" action="<?= h($controller->action('submit')) ?>" data-dialog-form>
    <?= $form->hidden('groupID', $group->getGroupID()) ?>
    <div class="ccm-ui">
        <?php
        $view->element('groups/delete_options', ['numGroups' => 1]);
        ?>
    </div>
    <div class="dialog-buttons">
        <button class="btn btn-secondary" data-dialog-action="cancel"><?= t('Cancel') ?></button>
        <button type="button" data-dialog-action="submit" class="btn btn-danger ms-auto"><?= t('Delete') ?></button>
    </div>
</form>
