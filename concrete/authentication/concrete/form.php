<?php defined('C5_EXECUTE') or die('Access Denied.');
$form = Core::make('helper/form');
$dh = Core::make('helper/date');  /** @var \Concrete\Core\Localization\Service\Date $dh */
/** @var Concrete\Core\Form\Service\Form $form */

$loggedIn = isset($user) && $user->isRegistered();
?>

<?php
if (!$loggedIn) {
    ?>
    <form class="concrete-login-form" method="post" action="<?= URL::to('/login', 'authenticate', $this->getAuthenticationTypeHandle()) ?>">

        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label" for="uName">
                <?= Config::get('concrete.user.registration.email_registration') ? t('Email Address') : t('User Name') ?>
            </label>
            <div class="col-sm-9">
                <input name="uName" id="uName" class="form-control" autofocus="autofocus" />
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label" for="uPassword">
                <?= t('Password') ?>
            </label>
            <div class="col-sm-9">
                <div class="input-group">
                    <input name="uPassword" id="uPassword" class="form-control" type="password" autocomplete="off" />
                    <button
                        type="button"
                        id="toggle-password-visibility"
                        class="btn btn-outline-secondary"
                        title="<?= t('Show password') ?>"
                        aria-label="<?= t('Show password') ?>"
                    ><i class="fas fa-eye" aria-hidden="true"></i></button>
                </div>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label" for="uPassword">
            </label>
            <div class="col-sm-9 text-end">
                <a href="<?= URL::to('/login', 'concrete', 'forgot_password') ?>" class="btn-link"><?= t('Forgot Password') ?></a>
            </div>
        </div>
        <?php
        if (Config::get('concrete.session.remember_me.lifetime') > 0) {
            ?>
            <div class="mb-3 row">
                <div class="col-sm-3 col-form-label pt-0"><?= t('Remember Me') ?></div>
                <div class="col-sm-9">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="uMaintainLogin" name="uMaintainLogin" value="1">
                        <label class="form-check-label form-check-remember-me" for="uMaintainLogin">
                            <?= t('Stay signed in for %s', $dh->describeInterval(Config::get('concrete.session.remember_me.lifetime'))) ?>
                        </label>
                    </div>
                </div>
            </div>
            <?php
        }

        if (isset($locales) && is_array($locales) && count($locales) > 0) {
            ?>
            <div class="mb-3">
                <label for="USER_LOCALE" class="control-label form-label"><?= t('Language') ?></label>
                <?= $form->select('USER_LOCALE', $locales) ?>
            </div>
            <?php
        }
        ?>
        <div class="mb-3 row">
            <div class="col-sm-12 text-end">
                <a href="<?= \URL::to('/') ?>" class="btn btn-secondary"> <?= t('Cancel') ?> </a>
                <button class="btn btn-primary"><?= t('Sign In') ?></button>
                <?php Core::make('helper/validation/token')->output('login_' . $this->getAuthenticationTypeHandle()) ?>
            </div>
        </div>

        <?php
        if (Config::get('concrete.user.registration.enabled')) {
            ?>
            <hr/>
            <div class="text-center sign-up-container">
                <?= t("Don't have an account?") ?>
                <a href="<?= URL::to('/register') ?>" class="btn-link"><?= t('Sign up') ?></a>
            </div>
            <?php
        }
        ?>
    </form>
    <script>
    $(function() {
        $('#toggle-password-visibility').on('click', function() {
            var $button = $(this);
            var $password = $('#uPassword');
            var showPassword = $password.attr('type') === 'password';
            var label = showPassword ? <?= json_encode(t('Hide password')) ?> : <?= json_encode(t('Show password')) ?>;

            $password.attr('type', showPassword ? 'text' : 'password');
            $button
                .attr('title', label)
                .attr('aria-label', label)
                .find('i')
                .toggleClass('fa-eye', !showPassword)
                .toggleClass('fa-eye-slash', showPassword)
            ;
        });
    });
    </script>
    <?php
} else {
    ?>
    <div class="alert alert-danger"><?= t('You are already logged in.') ?></div>
    <?php
}
?>
