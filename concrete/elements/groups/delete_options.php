<?php

use Concrete\Core\Form\Service\Form;
use Concrete\Core\User\Group\Command\DeleteGroupCommand;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var int $numGroups
 */

$form = app(Form::class);

?>
<fieldset>
    <legend><?= t('Options') ?></legend>
    <div>
        <div class="form-check">
            <?= $form->checkbox(
                'groups-delete-options-onlyIfEmpty',
                1,
                false,
                ['name' => 'onlyIfEmpty']
            ) ?>
            <?= $form->label('groups-delete-options-onlyIfEmpty', t("Don't delete groups that have members")) ?>
        </div>
    </div>
    <div class="row g-3 align-items-center">
        <div class="col-auto">
            <?= $form->label('groups-delete-options-subGroupsOperation', $numGroups > 1 ? t('If a group has sub-groups:') : t('If the group has sub-groups:')) ?>
        </div>
        <div class="col-auto">
            <?= $form->select(
                'groups-delete-options-subGroupsOperation',
                [
                    DeleteGroupCommand::ONCHILDGROUPS_MOVETOROOT => t('Move them to the root'),
                    DeleteGroupCommand::ONCHILDGROUPS_MOVETOPARENT => t('Move them to the parent group'),
                    DeleteGroupCommand::ONCHILDGROUPS_DELETE => t('Delete groups recursively'),
                    DeleteGroupCommand::ONCHILDGROUPS_ABORT => t('Abort operation'),
                ],
                DeleteGroupCommand::ONCHILDGROUPS_MOVETOROOT,
                ['name' => 'subGroupsOperation']
            ) ?>
        </div>
    </div>
</fieldset>
