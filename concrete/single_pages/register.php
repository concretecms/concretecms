<?php defined('C5_EXECUTE') or die("Access Denied.");

$token = \Core::make('Concrete\Core\Validation\CSRF\Token');
?>
<div class="row">
    <div class="col-sm-10 col-sm-offset-1">
        <div class="page-header">
            <h1><?= t('Site Registration') ?></h1>
        </div>
    </div>
</div>

<?php
$attribs = UserAttributeKey::getRegistrationList();

if (!empty($registerSuccess)) {
    ?>
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1">
            <?php switch ($registerSuccess) {
                case "registered":
                    ?>
                    <p><strong><?= $successMsg ?></strong><br/><br/>
                        <a href="<?= $view->url('/') ?>"><?= t('Return to Home') ?></a></p>
                    <?php
                    break;
                case "validate":
                    ?>
                    <p><?= $successMsg[0] ?></p>
                    <p><?= $successMsg[1] ?></p>
                    <p><a href="<?= $view->url('/') ?>"><?= t('Return to Home') ?></a></p>
                    <?php
                    break;
                case "pending":
                    ?>
                    <p><?= $successMsg ?></p>
                    <p><a href="<?= $view->url('/') ?>"><?= t('Return to Home') ?></a></p>
                    <?php
                    break;
            }
            ?>
        </div>
    </div>
    <?php

} else {
    ?>
    <form method="post" action="<?= $view->url('/register', 'do_register') ?>" class="form-stacked">
        <?php $token->output('register.do_register') ?>
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <fieldset>
                    <legend><?= t('Your Details') ?></legend>
                    <?php
                    if ($displayUserName) {
                        ?>
                        <div class="form-group">
                            <?= $form->label('uName', t('Username')) ?>
                            <?= $form->text('uName') ?>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="form-group">
                        <?= $form->label('uEmail', t('Email Address')) ?>
                        <?= $form->text('uEmail') ?>
                    </div>
                    <div class="form-group">
                        <?= $form->label('uPassword', t('Password')) ?>
                        <?= $form->password('uPassword', array('autocomplete' => 'off')) ?>
                    </div>
                    <?php
                    if (Config::get('concrete.user.registration.display_confirm_password_field')) {
                        ?>
                        <div class="form-group">
                            <?= $form->label('uPasswordConfirm', t('Confirm Password')) ?>
                            <?= $form->password('uPasswordConfirm', array('autocomplete' => 'off')) ?>
                        </div>
                        <?php
                    }
                    ?>

                </fieldset>
            </div>
        </div>
        <?php
        if (count($attribs) > 0) {
            ?>
            <div class="row">
                <div class="col-sm-10 col-sm-offset-1">
                    <fieldset>
                        <legend><?= t('Options') ?></legend>
                        <?php
                        foreach ($attribs as $ak) {
                            $renderer->buildView($ak)->setIsRequired($ak->isAttributeKeyRequiredOnRegister())->render();
                        }
                        ?>
                    </fieldset>
                </div>
            </div>
            <?php

        }
        if (Config::get('concrete.user.registration.captcha')) {
            ?>
            <div class="row">
                <div class="col-sm-10 col-sm-offset-1 ">

                    <div class="form-group">
                        <?php
                        $captcha = Loader::helper('validation/captcha');
                        echo $captcha->label();
                        ?>
                        <?php
                        $captcha->showInput();
                        $captcha->display();
                        ?>
                    </div>
                </div>
            </div>

            <?php
        }
        ?>
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <div class="form-actions">
                    <?= $form->hidden('rcID', isset($rcID) ? $rcID : '');
                    ?>
                    <?= $form->submit('register', t('Register') . ' &gt;', array('class' => 'btn-lg btn-primary')) ?>
                </div>
            </div>
        </div>
    </form>

    <?php

}
?>
