<?php
if (isset($user) && is_object($user)) {
    $dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
    ?>

    <style type="text/css">
        div[data-container=editable-fields] section {
            margin-bottom: 30px;
            position: relative;
            border-bottom: 1px solid #f1f1f1;
            padding-bottom: 30px;
            clear: both;
        }
    </style>

    <div data-container="editable-fields">

        <section>
            <div class="row">

                <div class="col-md-6">
                    <h4><?= t('Basic Details') ?></h4>
                    <div class="row">
                        <div class="col-md-4"><p><?= t('Username') ?></p></div>
                        <div class="col-md-8"><p><span
                            <?php
                            if ($canEditUserName) {
                                ?>
                                data-editable-field-type="xeditable"
                                data-url="<?= $view->action('update_username', $user->getUserID()) ?>"
                                data-type="text"
                                data-name="uName"
                                <?php
                            }
                            ?>
                        ><?= h($user->getUserName()) ?></span></p></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><p><?= t('Email Address') ?></p></div>
                        <div class="col-md-8"><p><span
                        <?php
                            if ($canEditEmail) {
                                ?>
                                data-editable-field-type="xeditable"
                                data-url="<?= $view->action('update_email', $user->getUserID()) ?>"
                                data-type="email"
                                data-name="uEmail"
                                <?php
                            }
                            ?>
                        ><?= h($user->getUserEmail()) ?></span></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><p><?= t('Password') ?></p></div>
                        <div class="col-md-8"><p>
                            <?php
                            if ($canEditPassword) {
                                ?><a href="#" class="btn btn-xs btn-default" data-button="change-password"><?= t('Change') ?></a><?php
                            } else {
                                ?>*********<?php
                            }
                            ?>
                        </p></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><p><?= t('Profile Picture') ?></p></div>
                        <div class="col-md-8"><p><div
                            <?php
                            if ($canEditAvatar) {
                                ?>
                                data-editable-field-type="image"
                                data-editable-field-inline-commands="true"
                                data-url="<?= $view->action('update_avatar', $user->getUserID()) ?>"
                                <?php
                            }
                            ?>
                        >
                            <ul class="ccm-edit-mode-inline-commands">
                                <li><a href="#" data-editable-field-command="clear"><i class="fa fa-trash-o"></i></a></li>
                            </ul>
                            <span class="editable-image-wrapper">
                                <input type="file" id="file-avatar" name="avatar"/>
                                <div class="editable-image-display"><?=$user->getUserAvatar()->output() ?></div>
                            </span>
                        </div></p></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <h4><?= t('Account') ?></h4>
                    <div class="row">
                        <div class="col-md-4"><p><?= t('Date Created') ?></p></div>
                        <div class="col-md-8"><p><?= $dh->formatDateTime($user->getUserDateAdded()) ?></p></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><p><?= t('Last IP Address') ?></p></div>
                        <div class="col-md-8"><p><?= $user->getLastIPAddress() ?></p></div>
                    </div>
                    <?php
                    if (Config::get('concrete.misc.user_timezones')) {
                        $uTimezone = $user->getUserTimezone();
                        if (empty($uTimezone)) {
                            $uTimezone = date_default_timezone_get();
                        }
                        ?>
                        <div class="row">
                            <div class="col-md-4"><p><?= t('Timezone') ?></p></div>
                            <div class="col-md-8"><p><span
                            <?php
                            if ($canEditTimezone) {
                                ?>
                                data-editable-field-type="xeditable"
                                data-source="<?= $view->action('get_timezones') ?>"
                                data-url="<?= $view->action('update_timezone', $user->getUserID()) ?>"
                                data-type="select" data-name="uTimezone"
                                data-value="<?= h($uTimezone) ?>"
                                <?php
                            }
                            ?>
                            ><?= $dh->getTimezoneDisplayName($uTimezone) ?></span></p></div>
                        </div>
                    <?php
                    }
                    $languages = Localization::getAvailableInterfaceLanguages();
                    if (count($languages) > 0) {
                        ?>
                        <div class="row">
                            <div class="col-md-4"><p><?= t('Language') ?></p></div>
                            <div class="col-md-8"><p><span
                            <?php
                            if ($canEditLanguage) {
                                ?>
                                data-editable-field-type="xeditable"
                                data-source="<?= $view->action('get_languages') ?>"
                                data-url="<?= $view->action('update_language', $user->getUserID()) ?>"
                                data-type="select"
                                data-name="uDefaultLanguage"
                                data-value="<?= h($user->getUserDefaultLanguage()) ?>"
                                <?php
                            }
                            ?>
                            ><?= h(Punic\Language::getName($user->getUserDefaultLanguage())) ?></span></p></div>
                        </div>
                        <?php
                    }
                    if (Config::get('concrete.user.registration.validate_email')) {
                        ?>
                        <div class="row">
                            <div class="col-md-4"><p><?= t('Full Record') ?></p></div>
                            <div class="col-md-8"><p><?= ($user->isFullRecord()) ? t('Yes') : t('No') ?></p></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><p><?= t('Email Validated') ?></p></div>
                            <div class="col-md-8"><p>
                                <?php
                                switch ($user->isValidated()) {
                                    case '-1':
                                        print t('Unknown');
                                        break;
                                    case '0':
                                        print t('No');
                                        break;
                                    case '1':
                                        print t('Yes');
                                        break;
                                }
                                ?>
                            </p></div>
                        </div>
                        <?php
                    }
                    ?>

                    <h4><?= t('Groups') ?></h4>
                    <div data-container="group-list"></div>
                    <?php
                    if ($canAddGroup) {
                        ?>
                        <hr>
                        <a class="btn btn-default btn-xs" data-button="assign-groups" dialog-width="640"
                           dialog-height="480" dialog-modal="true"
                           href="<?= URL::to('/ccm/system/dialogs/group/search') ?>?filter=assign"
                           dialog-title="<?= t('Add Groups') ?>" dialog-modal="false"><?= t('Add Group') ?></a>
                        <?php
                    }
                    ?>
                </div>

            </div>
        </section>

        <section>

            <?php foreach ($attributeSets as $set) : ?>
                <h4><?php echo $set->getAttributeSetDisplayName()?></h4>
                <?php
                    View::element('attribute/editable_list', [
                        'attributes' => $set->getAttributeKeys(),
                        'object' => $user,
                        'saveAction' => $view->action('update_attribute', $user->getUserID()),
                        'clearAction' => $view->action('clear_attribute', $user->getUserID()),
                        'permissionsArguments' => ['attributes' => $allowedEditAttributes],
                        'permissionsCallback' => function ($ak, $permissionsArguments) {
                            return is_array($permissionsArguments['attributes']) && in_array($ak->getAttributeKeyID(), $permissionsArguments['attributes']);
                        },
                    ]); ?>
            <?php endforeach; ?>

            <?php if (count($unassigned)) :
                if (count($attributeSets)) { ?>
                    <h4><?php echo t('Other')?></h4>
                <?php
                }
                View::element('attribute/editable_list', [
                    'attributes' => $unassigned,
                    'object' => $user,
                    'saveAction' => $view->action('update_attribute', $user->getUserID()),
                    'clearAction' => $view->action('clear_attribute', $user->getUserID()),
                    'permissionsArguments' => ['attributes' => $allowedEditAttributes],
                    'permissionsCallback' => function ($ak, $permissionsArguments) {
                        return is_array($permissionsArguments['attributes']) && in_array($ak->getAttributeKeyID(), $permissionsArguments['attributes']);
                    },
                ]); ?>
            <?php endif; ?>
        </section>

    </div>

    <?php
    if ($canEditPassword) {
        ?>
        <div style="display: none">
            <div data-dialog="change-password" class="ccm-ui">
                <form data-dialog-form="change-password" action="<?= $view->action('change_password', $user->getUserID()) ?>">
                    <?= $token->output('change_password') ?>

                    <div class="form-group">
                        <?= $form->label('uPassword', t('Password')) ?>
                        <?= $form->password('uPassword', ['autocomplete' => 'off']) ?>
                    </div>

                    <div class="form-group">
                        <?= $form->label('uPasswordConfirm', t('Confirm Password')) ?>
                        <?= $form->password('uPasswordConfirm', ['autocomplete' => 'off']) ?>
                    </div>

                    <div class="dialog-buttons">
                        <button class="btn btn-default pull-left" data-dialog-action="cancel"><?= t('Cancel') ?></button>
                        <button type="button" data-dialog-action="submit" class="btn btn-primary pull-right"><?= t('Update') ?></button>
                    </div>

                </form>
            </div>
        </div>
        <?php
    }
    ?>

    <script type="text/template" data-template="user-add-groups">
        <% _.each(groups, function(group) { %>
            <div class="row" data-editable-field-inline-commands="true" data-group-id="<%=group.gID%>">
                <ul class="ccm-edit-mode-inline-commands">
                    <li><a href="#" data-group-id="<%=group.gID%>" data-button="delete-group"><i class="fa fa-trash-o"></i></a></li>
                </ul>
                <div class="col-md-6"><p><%=group.gDisplayName%></p></div>
                <div class="col-md-6"><p><%=group.gDateTimeEntered%></p></div>
            </div>
        <% }); %>
    </script>

    <script type="text/javascript">
        $(function () {

            var _addGroupsTemplate = _.template($('script[data-template=user-add-groups]').html());
            $('div[data-container=group-list]').append(
                _addGroupsTemplate({'groups': <?=$groupsJSON?>})
            );
            $('div[data-container=editable-fields]').concreteEditableFieldContainer({
                url: '<?=$view->action('save', $user->getUserID())?>',
                data: {
                    ccm_token: '<?=$token->generate()?>'
                }
            });

            $('button[name=task][value=sudo]').on('click', function () {
                return confirm('<?=t("This will end your current session and sign you in as %s", $user->getUserName())?>');
            });

            $('button[name=task][value=delete]').on('click', function () {
                return confirm('<?=t("Are you sure you want to permanently remove this user?")?>');
            });
            ConcreteEvent.subscribe('SelectGroup', function (e, data) {
                $.concreteAjax({
                    url: "<?=URL::to('/ccm/system/user/add_group')?>",
                    data: {
                        gID: data.gID,
                        uID: '<?=$user->getUserID()?>',
                        ccm_token: '<?= $token->generate('add_group') ?>'
                    },
                    success: function (r) {
                        $('div[data-container=group-list]').append(
                            _addGroupsTemplate({'groups': r.groups})
                        );
                        _.each(r.groups, function (group) {
                            $('div[data-container=group-list] div[data-group-id=' + group.gID + ']').addClass('animated bounceIn');
                        });
                        jQuery.fn.dialog.closeTop();
                    }
                });
            });

            $('div[data-container=editable-fields]').on('click', 'a[data-button=change-password]', function () {
                $.fn.dialog.open({
                    element: 'div[data-dialog=change-password]',
                    title: '<?=t('Change Password')?>',
                    width: '280',
                    height: '220',
                    modal: true,
                    close: function () {
                        $(this).find('input[type=password]').val('');
                    }
                });
                return false;
            });

            $('div[data-container=editable-fields]').on('click', 'a[data-button=delete-group]', function () {
                $.concreteAjax({
                    url: "<?=URL::to('/ccm/system/user/remove_group')?>",
                    data: {
                        gID: $(this).attr('data-group-id'),
                        uID: '<?=$user->getUserID()?>',
                        ccm_token: '<?= $token->generate('remove_group') ?>'
                    },
                    success: function (r) {
                        $('div[data-container=group-list] div[data-group-id=' + r.group.gID + ']').queue(function () {
                            $(this).addClass('animated bounceOutLeft');
                            $(this).dequeue();
                        }).delay(500).queue(function () {
                            $(this).remove();
                            $(this).dequeue();
                        })
                        jQuery.fn.dialog.closeTop();
                    }
                });
                return false;
            });

            $('a[data-button=assign-groups]').dialog();

        });
    </script>
    <?php
    if (is_array($workflowList)) {
        View::element('workflow/notifications', [
            'workflowList' => $workflowList,
        ]);
    }
    ?>
    <?php
} else {
    $tp = Core::make('helper/concrete/user');
    if ($tp->canAccessUserSearchInterface()) {
        ?>

        <div class="ccm-dashboard-content-full">
            <?php View::element('users/search', ['result' => $result])?>
        </div>

        <script type="text/javascript">
            $(function() {
                $('#ccm-dashboard-content').concreteAjaxSearch({
                    result: <?=json_encode($result->getJSONObject())?>,
                    onLoad: function (concreteSearch) {
                        concreteSearch.$element.on('click', 'a[data-user-id]', function () {
                            window.location.href = '<?=rtrim(URL::to('/dashboard/users/search', 'view'), '/')?>/' + $(this).attr('data-user-id');
                            return false;
                        });
                    }
                });
            });
        </script>

        <?php
    } else {
        ?>
        <p><?= t('You do not have access to user search. This setting may be changed in the access section of the dashboard settings page.') ?></p>
        <?php
    }

}
