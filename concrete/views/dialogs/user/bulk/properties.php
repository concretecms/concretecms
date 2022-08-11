<?php
defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\Dialog\User\Bulk\Properties $controller
 * @var Concrete\Core\Filesystem\Element $keySelector
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\User\UserInfo[] $users
 */
?>

<form method="post" action="<?=$controller->action('submit')?>" data-dialog-form="users-attributes">
    <?php
    foreach ($users as $user) {
        echo $form->hidden("item{$user->getUserID()}", $user->getUserID(), ['name' => 'item[]']);
    }
    ?>

    <div class="ccm-ui">
        <?php
            $keySelector->render();
        ?>
    </div>

    <div class="dialog-buttons">
        <button class="btn btn-secondary" data-dialog-action="cancel"><?=t('Cancel')?></button>
        <button type="button" data-dialog-action="submit" class="btn btn-primary ms-auto"><?=t('Save')?></button>
    </div>

</form>

