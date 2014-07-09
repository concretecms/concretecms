<?php defined('C5_EXECUTE') or die('Access denied.');

$activeAuths = AuthenticationType::getActiveListSorted();
$form = Loader::helper('form');

$active = null;
if ($authType) {
    $active = $authType;
    $activeAuths = array($authType);
}
?>

<style>
    .login-title {
        padding: 0;
        margin-bottom: 65px;
        color: white;
        text-shadow: 3px 3px 7px rgba(0, 0, 0, .2);
        font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
        font-weight: 300;
    }

    .login-form {
        height: 460px;
    }

    .login-form > .row {
        height: 100%;
    }

    .types {
        color: #9b9b9b;
        padding: 15px 35px;
        background-color: rgba(0, 0, 0, .7);
        height: 100%;
    }

    .controls {
        color: #9b9b9b;
        background-color: rgba(0, 0, 0, .9);
        padding: 35px;
        height: 100%;
        font-size: 16px;
        font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
        font-weight: 300;
    }

    .controls hr {
        border-color: #9b9b9b;
    }

    ul.auth-types {
        margin: 0;
        padding: 0;
    }

    ul.auth-types > li > .fa {
        margin-right: 10px;
    }

    ul.auth-types > li {
        list-style-type: none;
        padding: 15px 0px;
        cursor: pointer;
        transition: color .25s;
    }

    ul.auth-types > li:hover {
        color: #cfcfcf;
    }

    ul.auth-types > li.active {
        color: white;
        cursor: auto;
    }
</style>
<div class="col-sm-6 col-sm-offset-3 login-title">
    <span>Sign into your website.</span>
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
                        <i class="fa fa-user"></i>
                        <span><?= $auth->getAuthenticationTypeName() ?></span>
                    </li>
                <?php
                }
                ?>
            </ul>
        </div>
        <div class="controls col-sm-8">
            <?php
            /** @var AuthenticationType[] $activeAuths */

            foreach ($activeAuths as $auth) {
                ?>
                <div data-handle="<?= $auth->getAuthenticationTypeHandle() ?>"
                     class="authentication-type authentication-type-<?= $auth->getAuthenticationTypeHandle() ?>">
                     <?php $auth->renderForm($authTypeElement ?: 'form', $authTypeParams ?: array()) ?>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    (function ($) {
        "use strict";

        var forms = $('div.controls').find('div.authentication-type').hide();
        var types = $('ul.auth-types > li').each(function() {
            var me = $(this),
                form = forms.filter('[data-handle="' + me.data('handle') + '"]');
            me.click(function() {
                if (form.hasClass('active')) return;
                types.removeClass('active');
                me.addClass('active')
                if (forms.filter('.active').length) {
                    forms.stop().filter('.active').removeClass('active').fadeOut(250, function() {
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
            $.backstretch("<?= DASHBOARD_BACKGROUND_FEED . '/' . date('Ymd') ?>.jpg", {
                fade: 500
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
