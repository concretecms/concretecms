<?php
if (isset($error)) {
    ?>
    <div class="alert alert-danger"><?= $error ?></div>
    <?php
}
if (isset($message)) {
    ?>
    <div class="alert alert-success"><?= $message ?></div>
<?php
}

$user = new User;

if ($user->isLoggedIn()) {
    ?>
    <a href="<?= \URL::to('/system/authentication/community/attempt_attach'); ?>">
        <?= t('Attach a community account') ?>
    </a>
    <?php
} else {
    ?>
    <a href="<?= \URL::to('/system/authentication/community/attempt_auth'); ?>">
        <?= t('Log in With community') ?>
    </a>
    <?php
}


?>
<script>
    (function() {
        var svg = $('.ccm-concrete-authentication-type-svg > svg');


        if (svg) {
            var img = new Image();
            img.onerror = function() {
                svg.parent().replaceWith('<i class="fa fa-user"></i>');
            };
            img.src = svg.parent().data('src');
            $(function() {

                if (svg.closest('li').hasClass('active')) {
                    svg.attr('fill', 'white');
                } else {
                    svg.attr('fill', 'rgb(155,155,155)');
                }
                Concrete.event.bind('AuthenticationTypeSelected', function(e, handle) {
                    if (handle === 'community') {
                        svg.attr('fill', 'white');
                    } else {
                        svg.attr('fill', 'rgb(155,155,155)');
                    }
                });

            });
        }
    }());
</script>
