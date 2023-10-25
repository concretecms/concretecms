<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\Dialog\Groups\Bulk\Delete $controller
 * @var Concrete\Core\View\DialogView $view
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\User\Group\Group[] $groups
 */

if ($groups === []) {
    ?>
    <div class="alert-message info">
        <?= t('No groups are eligible for this operation') ?>
    </div>
    <?php
    return;
}
?>
<p><?= t('Are you sure you would like to delete the following groups?') ?></p>
<form method="POST" data-dialog-form="delete-groups" action="<?= h($controller->action('submit')) ?>" data-dialog-form>
    <?php
    foreach ($groups as $group) {
        echo $form->hidden('item[]', $group->getGroupID());
    }
    ?>
    <div class="ccm-ui">
        <?php
        $view->element('groups/confirm_list', ['groups' => $groups]);
        $view->element('groups/delete_options', ['numGroups' => count($groups)]);
        ?>
    </div>
    <div class="dialog-buttons">
        <button class="btn btn-secondary" data-dialog-action="cancel"><?= t('Cancel') ?></button>
        <button type="button" data-dialog-action="submit" class="btn btn-danger ms-auto"><?= t('Delete') ?></button>
    </div>
</form>
