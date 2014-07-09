<?php defined('C5_EXECUTE') or die('Access denied.') ?>

<button class="btn btn-block btn-success authFacebookLogin"><?= t('Log in with facebook') ?></button>
<script type="text/javascript">
    $('button.authFacebookLogin').click(function () {
        var login = window.open('<?=$loginUrl?>', 'Log in with Facebook', 'width=500,height=300');
        (login.focus && login.focus());

        function loginStatus() {
            if (login.closed) {
                window.location.href = '<?=$statusURI?>';
                return;
            }
            setTimeout(loginStatus, 500);
        }

        loginStatus();
        return false;
    });
</script>
