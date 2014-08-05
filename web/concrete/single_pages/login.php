<?php defined('C5_EXECUTE') or die('Access denied.');

$activeAuths = AuthenticationType::getActiveListSorted();
$form = Loader::helper('form');

$active = null;
if ($authType) {
    $active = $authType;
    $activeAuths = array($authType);
}
$image = date('Ymd') . '.jpg';
?>
<div class="login-page">
    <div class="col-sm-6 col-sm-offset-3 login-title">
        <span><?= t('Sign into your website.') ?></span>
    </div>
    <div class="col-sm-6 col-sm-offset-3 login-form">
        <div class="row">
            <div class="types col-sm-4">
                <ul class="auth-types">
                    <?php
                    /** @var AuthenticationType[] $activeAuths */

                    foreach ($activeAuths as $auth) {
                        ?>
                        <li data-handle="<?= $auth->getAuthenticationTypeHandle() ?>">
                            <?=$auth->getAuthenticationTypeIconHTML()?>
                            <span><?= $auth->getAuthenticationTypeName() ?></span>
                        </li>
                    <?php
                    }
                    ?>
                </ul>
				<?php if ($user->isLoggedIn()) { ?>
					<ul class="auth-types logout" style="position: absolute;bottom: 0;padding-bottom: 15px;">
						<li data-handle="logout">
							<i class="fa fa-power-off"></i>
							<span>Logout</span>
						</li>
					</ul>
				<?php } ?>
            </div>
            <div class="controls col-sm-8">
                <?php
                /** @var AuthenticationType[] $activeAuths */

                foreach ($activeAuths as $auth) {
                    ?>
                    <div data-handle="<?= $auth->getAuthenticationTypeHandle() ?>"
                         class="authentication-type authentication-type-<?= $auth->getAuthenticationTypeHandle() ?>">
                        <?php $auth->renderForm($authTypeElement ? : 'form', $authTypeParams ? : array()) ?>
                    </div>
                <?php
                }
                ?>
				<div data-handle="logout" class="authentication-type authentication-type-logout">
                    <?php View::element('users/logout_form') ?>
				</div>
            </div>
        </div>
    </div>
    <div class="background-credit">
        <?= t('Photo Credit:') ?>
        <a href="#" style="pull-right"></a>
    </div>

    <script type="text/javascript">
        (function ($) {
            "use strict";

            var forms = $('div.controls').find('div.authentication-type').hide();
            var types = $('ul.auth-types > li').each(function () {
                var me = $(this),
                    form = forms.filter('[data-handle="' + me.data('handle') + '"]');
                me.click(function () {
                    if (form.hasClass('active')) return;
                    types.removeClass('active');
                    me.addClass('active')
                    if (forms.filter('.active').length) {
                        forms.stop().filter('.active').removeClass('active').fadeOut(250, function () {
                            form.addClass('active').fadeIn(250);
                        });
                    } else {
                        form.addClass('active').show();
                    }
                });
            });
            types.first().click();

            var title = $('.login-title').find('span');
            title.css({
                lineHeight: '1000px',
                fontSize: 10
            });
            setTimeout(function () {
                var start_height = title.parent().height(), size = 10, last;
                while (title.parent().height() === start_height) {
                    last = size++;
                    title.css('font-size', size);
                }
                title.css({
                    fontSize: last,
                    lineHeight: 'auto'
                });
            }, 0);

            $(function () {
                $.backstretch("<?= DASHBOARD_BACKGROUND_FEED . '/' . $image ?>", {
                    fade: 500
                });
                $.getJSON('<?= BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '/tools/required/dashboard/get_image_data' ?>', { image: '<?= $image ?>' }, function (data) {
                    console.log($('div.background-credit').children().attr('href', data.link).text(data.author.join()));
                    console.log(data);
                });
            });
            $('ul.nav.nav-tabs > li > a').on('click', function () {
                var me = $(this);
                if (me.parent().hasClass('active')) return false;
                $('ul.nav.nav-tabs > li.active').removeClass('active');
                var at = me.attr('data-authType');
                me.parent().addClass('active');
                $('div.authTypes > div').hide().filter('[data-authType="' + at + '"]').show();
                return false;
            });
        })(jQuery);
    </script>
</div>
