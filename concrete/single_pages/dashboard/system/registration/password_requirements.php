<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Core\Page\View\PageView $view */

/* @var int $min */
/* @var int $max */
/* @var int $specialCharacters */
/* @var int $upperCase */
/* @var int $lowerCase */
/* @var int $passwordReuse */
/* @var array $customRegex */
/* @var string $saveAction */

?>

<form action="<?= $saveAction ?>" method="POST">
    <?php $token->output('save_password_requirements') ?>

    <fieldset>
        <legend><?= t('User Password Requirements') ?></legend>
        <div class="form-group">
            <label style="font-weight: normal">
                <?= t('Require at least %s minimum characters', $form->number('min', $min ?: 0,
                    ["style" => "width:80px; display:inline-block;"])) ?>
            </label>
        </div>
        <div class="form-group">
            <label style="font-weight: normal">
                <?= t('Require at least %s maximum characters', $form->number('max', $max ?: 0,
                    ["style" => "width:80px; display:inline-block;"])) ?>
            </label>
        </div>
        <div class="form-group">
            <label style="font-weight: normal">
                <?= t('Require at least %s special characters',
                    $form->number('specialCharacters', $specialCharacters ?: 0,
                        ["style" => "width:80px; display:inline-block;"])) ?>
            </label>
        </div>
        <div class="form-group">
            <label style="font-weight: normal">
                <?= t('Require at least %s lowercase characters', $form->number('lowerCase', $lowerCase ?: 0,
                    ["style" => "width:80px; display:inline-block;"])) ?>
            </label>
        </div>
        <div class="form-group">
            <label style="font-weight: normal">
                <?= t('Require at least %s uppercase characters', $form->number('upperCase', $upperCase ?: 0,
                    ["style" => "width:80px; display:inline-block;"])) ?>
            </label>
        </div>
        <div class="form-group">
            <div class="form-inline">
                <div class="checkbox">
                    <label>
                        <?= $form->checkbox('isPasswordReuse', '1', $passwordReuse > 0) ?>
                        <?= t('Prevent password reuse') ?>
                    </label>
                    <div style="margin-left:40px;display:none" id="previousPasswordWell">
                        <label><?= t('Track previous passwords') ?></label>
                        <?= $form->number('passwordReuse', $passwordReuse ?: 0,
                            ["style" => "width:80px; display:inline-block;"]) ?>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend><?= t('Custom Regular Expression Requirements') ?>
            <a href="#" class="add-regex btn btn-default"><i class="fa fa-plus"></i> Add</a>
        </legend>
        <p class="bg-info" style="padding:10px;"><?= t('One or more custom regular expressions which the password must match in order for it to be valid. The password will be tested against each regular expression in order.') ?></p>

        <div class="row">
            <div class="col-xs-7">
                <label><?= t('Regular Expression') ?></label>
            </div>
            <div class="col-xs-4">
                <label><?= t('Description (optional)') ?></label>
            </div>
            <div class="col-xs-1"></div>
        </div>

        <div class="regex-container">
            <?php foreach ($customRegex as $regex => $value) { ?>
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-7">
                            <input type="text" class="form-control regex-input" name="regex[]" autocomplete="off"
                                   value="<?= h($regex) ?>"/>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="regex_desc[]" autocomplete="off"
                                   value="<?= h($value) ?>"/>
                        </div>
                        <div class="col-sm-1">
                            <a href="#" class="btn btn-default remove-regex"><i class="fa fa-trash"></i></a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <div class="pull-right">
                <a href="#" class="restore-defaults-btn btn btn-default"><?= t('Restore to Default Values') ?></a>
                <button class="btn btn-primary" type="submit"><?= t('Save') ?></button>
            </div>
        </div>
    </div>
</form>


<div style="display: none;">
    <div data-dialog-wrapper="restore-defaults" id="restore-defaults-dialog">
        <form method="post" action="<?php echo $view->action('reset'); ?>">
            <?php echo Loader::helper('validation/token')->output('restore_defaults'); ?>
            <h4><?= t("Are you sure you want to restore to default values?") ?></h4>
            <p><?= t("This action cannot be undone.") ?></p>
            <div class="dialog-buttons">
                <button class="btn btn-default pull-left"
                        onclick="jQuery.fn.dialog.closeTop()"><?= t('Cancel') ?></button>
                <button class="btn btn-primary pull-right"
                        onclick="$('div[data-dialog-wrapper=restore-defaults] form').submit()"><?= t('Restore Defaults') ?></button>
            </div>
        </form>
    </div>
</div>

<script>
  (function () {


    if ($('input[name="isPasswordReuse"]').is(':checked')) {
      $('#previousPasswordWell').show();
    }

    $('input[name="isPasswordReuse"]').change(function () {
      if (!$(this).is(':checked')) {
        $('#passwordReuse').val(0);
      }
      $('#previousPasswordWell').toggle();
    });


    $('.add-regex').click(function (e) {
      e.preventDefault();
      $('.regex-container').append(`
        <div class="form-group">
            <div class="row">
                <div class="col-sm-7">
                    <input type="text" class="form-control regex-input" name="regex[]" autocomplete="off"/>
                </div>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="regex_desc[]" autocomplete="off"/>
                </div>
                <div class="col-sm-1">
                    <a href="#" class="btn btn-default remove-regex"><i class="fa fa-trash"></i></a>
                </div>
            </div>
        </div>
      `);
      $('.regex-input').last().focus();
    });

    $('.regex-container').on('click', '.remove-regex', function (e) {
      e.preventDefault();
      $(this).parent().parent().remove();
    });

    $('.restore-defaults-btn').click(function (e) {
      e.preventDefault();
      var $element = $('#restore-defaults-dialog');
      jQuery.fn.dialog.open({
        element: $element,
        modal: true,
        width: 320,
        title: 'Restore Password Options',
        height: 'auto'
      });
    })
  })()
</script>
