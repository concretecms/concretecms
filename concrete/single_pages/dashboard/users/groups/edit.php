<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\Application\Service\UserInterface;
use Concrete\Core\Form\Service\Widget\DateTime;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\User\Group\GroupType;
use Concrete\Core\Utility\Service\Text;

/**
 * @var Concrete\Core\View\View $view
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\User\Group\Group $group
 * @var string $whyUserCantDeleteGroup
 */

$app = app();
$ih = $app->make(UserInterface::class);
$date = $app->make(DateTime::class);
$af = $app->make(FileManager::class);
$text = $app->make(Text::class);
$urlResolver = $app->make(ResolverManagerInterface::class);
?>

<form method="POST" id="update-group-form" class="form-stacked" action="<?= h($view->url('/dashboard/users/groups/', 'update_group')) ?>" role="form">
    <?php $token->output('add_or_update_group') ?>
    <?= $form->hidden('gID', $group->getGroupID()) ?>
    <?php
    if ($whyUserCantDeleteGroup !== '') {
        ?>
        <div class="alert alert-info">
            <?= nl2br(h($whyUserCantDeleteGroup)) ?>
        </div>
        <?php
    }
    ?>
    <fieldset>
        <legend>
            <?= t('Group Details') ?>
        </legend>

        <div class="form-group">
            <?= $form->label('gName', t('Name')) ?>
            <?= $form->text('gName', $group->getGroupName()) ?>
        </div>

        <div class="form-group">
            <?= $form->label('gDescription', t('Description')) ?>
            <?= $form->textarea('gDescription', $text->entities($group->getGroupDescription()), ['rows' => 6]) ?>
        </div>

        <div class="form-group">
            <?= $form->label('gtID', t('Group Type')) ?>
            <?= $form->select('gtID', GroupType::getSelectList(), $group->getGroupTypeId()) ?>

            <div class="help-block">
                <?= t(
                    'Click %shere%s to manage the group types.',
                    '<a href="' . h((string) $urlResolver->resolve(['/dashboard/users/group_types'])) . '">',
                    '</a>'
                ) ?>
            </div>
        </div>

        <div class="form-group">
            <?= $form->label('gOverrideGroupTypeSettings', t('Group Type Settings')) ?>
            <?= $form->select(
                'gOverrideGroupTypeSettings',
                [
                    0 => t('Inherit settings from group type'),
                    1 => t('Override settings from group type'),
                ],
                (int) $group->getOverrideGroupTypeSettings()
            ) ?>
        </div>

        <div class="form-group override-group-type-setting">
            <div class="form-check">
                <?= $form->checkbox('gtPetitionForPublicEntry', 1, $group->isPetitionForPublicEntry(), ['class' => 'form-check-input']) ?>
                <?= $form->label('gtPetitionForPublicEntry', t('Petition for public entry'), ['class' => 'form-check-label']) ?>
            </div>
        </div>

        <div class="form-group">
            <?= $form->label('gThumbnailFID', t('Thumbnail Image')) ?>
            <?= $af->image('gThumbnailFID', 'gThumbnailFID', t('Please select'), $group->getThumbnailImage()) ?>
        </div>
    </fieldset>

    <fieldset class="override-group-type-setting">
        <legend><?= t('Roles') ?></legend>

        <?php $view->element('groups/roles_list', ['roles' => $group->getRoles(), 'defaultRole' => $group->getDefaultRole()]) ?>
    </fieldset>

    <fieldset>
        <legend><?= t('Automation') ?></legend>

        <div class="form-group">
            <div class="form-check">
                <?= $form->checkbox('gIsAutomated', 1, $group->isGroupAutomated()) ?>
                <?= $form->label('gIsAutomated', t('This group is automatically entered.'), ['class' => 'form-check-label launch-tooltip', 'title' => t("Automated Groups aren't assigned by administrators. They are checked against code at certain times that determines whether users should enter them.")]) ?>
            </div>
        </div>

        <div id="gAutomationOptions" style="display: none">
            <div class="form-group">
                <?= $form->label('', t('Check Group')) ?>

                <div class="form-check">
                    <?= $form->checkbox('gCheckAutomationOnRegister', 1, $group->checkGroupAutomationOnRegister()) ?>
                    <?= $form->label('gCheckAutomationOnRegister', t('When a user registers.'), ['class' => 'form-check-label']) ?>
                </div>

                <div class="form-check">
                    <?= $form->checkbox('gCheckAutomationOnLogin', 1, $group->checkGroupAutomationOnLogin()) ?>
                    <?= $form->label('gCheckAutomationOnLogin', t('When a user signs in.'), ['class' => 'form-check-label']) ?>
                </div>

                <div class="form-check">
                    <?= $form->checkbox('gCheckAutomationOnJobRun', 1, $group->checkGroupAutomationOnJobRun()) ?>
                    <?= $form->label('gCheckAutomationOnJobRun', t('When the "Check Automated Groups" Job runs.'), ['class' => 'form-check-label']) ?>
                </div>
            </div>

            <div class="alert alert-info">
                <?= t('For custom automated group actions, make sure an automation group controller exists at %s', h($group->getGroupAutomationControllerClass())) ?>
            </div>
        </div>

        <div class="form-check">
            <?= $form->checkbox('gUserExpirationIsEnabled', 1, $group->isGroupExpirationEnabled()) ?>
            <?= $form->label('gUserExpirationIsEnabled', t('Automatically remove users from this group'), ['class' => 'form-check-label']) ?>
        </div>

        <div class="form-group">
            <?= $form->select(
                'gUserExpirationMethod',
                [
                    'SET_TIME' => t('at a specific date and time'),
                    'INTERVAL' => t('once a certain amount of time has passed'),
                ],
                $group->getGroupExpirationMethod(),
                ['disabled' => true, 'class' => 'form-control']
            ) ?>
        </div>

        <div id="gUserExpirationSetTimeOptions" style="display: none">
            <div class="form-group">
                <?= $form->label('gUserExpirationSetDateTime', t('Expiration Date')) ?>
                <?= $date->datetime('gUserExpirationSetDateTime', $group->getGroupExpirationDateTime()) ?>
            </div>
        </div>

        <div id="gUserExpirationIntervalOptions" style="display: none">
            <div class="form-group">
                <?= $form->label('', t('Accounts expire after')) ?>
                <?php
                $days = $group->getGroupExpirationIntervalDays();
                $hours = $group->getGroupExpirationIntervalHours();
                $minutes = $group->getGroupExpirationIntervalMinutes();
                $style = 'width: 60px';
                ?>
                <div>
                    <table class="table" style="width: auto">
                        <tr>
                            <th><?= t('Days') ?></th>
                            <th><?= t('Hours') ?></th>
                            <th><?= t('Minutes') ?></th>
                        </tr>
                        <tr>
                            <td><?= $form->text('gUserExpirationIntervalDays', $days, ['style' => $style]) ?></td>
                            <td><?= $form->text('gUserExpirationIntervalHours', $hours, ['style' => $style]) ?></td>
                            <td><?= $form->text('gUserExpirationIntervalMinutes', $minutes, ['style' => $style]) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div id="gUserExpirationAction" style="display: none">
            <div class="form-group">
                <?= $form->label('gUserExpirationAction', t('Expiration Action')) ?>
                <?= $form->select(
                    'gUserExpirationAction',
                    [
                        'REMOVE' => t('Remove the user from this group'),
                        'DEACTIVATE' => t('Deactivate the user account'),
                        'REMOVE_DEACTIVATE' => t('Remove the user from the group and deactivate the account'),
                    ],
                    $group->getGroupExpirationAction(),
                    ['class' => 'form-control']
                ) ?>
            </div>
        </div>

    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?= $view->url('/dashboard/users/groups') ?>" class="btn btn-secondary"><?= t('Cancel') ?></a>
            <button class="btn btn-primary float-end ms-2" type="submit"><?= t('Update Group') ?></button>
            <?php
            if ($whyUserCantDeleteGroup === '') {
                ?>
                <a class="btn btn-danger dialog-launch" dialog-modal="true" dialog-width="550" dialog-height="380" dialog-title="<?= t('Delete Group') ?>" href="<?= h($urlResolver->resolve(['/ccm/system/dialogs/groups/delete'])->setQuery(['groupID' => $group->getGroupID()])) ?>"><?= t('Delete') ?></a>
                <?php
            }
            ?>
        </div>
    </div>
</form>

<script>
    ccm_checkGroupExpirationOptions = function () {
        var sel = $("select[name=gUserExpirationMethod]");
        var cb = $("input[name=gUserExpirationIsEnabled]");
        if (cb.prop('checked')) {
            sel.attr('disabled', false);
            switch (sel.val()) {
                case 'SET_TIME':
                    $("#gUserExpirationSetTimeOptions").show();
                    $("#gUserExpirationIntervalOptions").hide();
                    break;
                case 'INTERVAL':
                    $("#gUserExpirationSetTimeOptions").hide();
                    $("#gUserExpirationIntervalOptions").show();
                    break;
            }
            $("#gUserExpirationAction").show();
        } else {
            sel.attr('disabled', true);
            $("#gUserExpirationSetTimeOptions").hide();
            $("#gUserExpirationIntervalOptions").hide();
            $("#gUserExpirationAction").hide();
        }
    }

    $(function () {
        $("input[name=gUserExpirationIsEnabled]").click(ccm_checkGroupExpirationOptions);
        $("select[name=gUserExpirationMethod]").change(ccm_checkGroupExpirationOptions);
        ccm_checkGroupExpirationOptions();
        $('input[name=gIsBadge]').on('click', function () {
            if ($(this).is(':checked')) {
                $('#gUserBadgeOptions').show();
            } else {
                $('#gUserBadgeOptions').hide();
            }
        }).triggerHandler('click');
        $('input[name=gIsAutomated]').on('click', function () {
            if ($(this).is(':checked')) {
                $('#gAutomationOptions').show();
            } else {
                $('#gAutomationOptions').hide();
            }
        }).triggerHandler('click');

        $("#gOverrideGroupTypeSettings").change(function() {
            if($(this).val() == 1) {
                $(".override-group-type-setting").removeClass("d-none");
            } else {
                $(".override-group-type-setting").addClass("d-none");
            }
        }).trigger("change");

        //$("#gtID").change(function () {
        //    if ($("#gOverrideGroupTypeSettings").val() == 0) {
        //        $(".ccm-role-list").data("ajaxRefreshRoleList")($(this).val());
        //    }
        //});
    });

</script>
