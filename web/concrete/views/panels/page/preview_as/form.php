<section>
    <header>
        <a href="" data-panel-navigation="back" class="ccm-panel-back">
            <span class="fa fa-chevron-left"></span>
        </a>
        <?=t('View as User')?>
    </header>
    <form class="preview-panel-form form-horizontal">
        <div class="ccm-panel-content-inner" id="ccm-menu-page-attributes-list">

            <label class="label"><?= t('Date') ?></label>
            <input class="form-control" type="datetime" value="<?= date('m/d/Y') ?>" />


            <label class="label"><?= t('Time') ?></label>
            <div class="input-group">
                <select class="hour form-control">
                    <?php
                    $i = 12;
                    $now = intval(date('h'));
                    while ($i--) {
                        $num = 12 - $i;
                        if ($num < 10) $num = "0" . $num;
                        ?>
                        <option<?= $now === intval($num, 10) ? ' selected' : '' ?>><?= $num ?></option>
                    <?php
                    }
                    ?>
                </select>
                <span class="input-group-addon" style="background-color:transparent;border:none;color:white">:</span>
                <select class="minute form-control">
                    <?php
                    $i = 60;
                    $now = intval(date('i'), 10);
                    while ($i--) {
                        $num = 59 - $i;
                        if ($num < 10) $num = "0" . $num;
                        ?>
                        <option<?= $now === intval($num, 10) ? ' selected' : '' ?>><?= $num ?></option>
                    <?php
                    }
                    ?>
                </select>
                <span class="input-group-addon" style="background-color:transparent;border:none;padding:2px"></span>
                <select class="ampm form-control">
                    <option<?= date('a') === 'am' ? ' selected' : ''?>><?= t('am') ?></option>
                    <option<?= date('a') !== 'am' ? ' selected' : ''?>><?= t('pm') ?></option>
                </select>
            </div>

            <label class="label"><?= t('View As') ?></label>
            <div>
                <div class="btn-group">
                    <button class="guest-button btn btn-default active"><?= t('Guest') ?></button>
                    <button class="user-button btn btn-default"><?= t('Site User') ?></button>
                </div>
                <div class="site-user" style="display:none">
                    <label for="user" class="label"><?= t('User') ?></label>
                    <input class="form-control custom-user" name="user" />
                </div>
            </div>

            <label class="label">

            </label>
        </div>
    </form>
</section>

<script type="application/javascript">
    (function(global, $) {
        'use strict';

        // User
        var user_input = $('div.site-user'),
            guest_button = $('button.guest-button'),
            user_button = $('button.user-button');

        guest_button.click(function(e) {
            if (!guest_button.hasClass('active')) {
                user_input.slideUp();
                user_button.removeClass('active');
                guest_button.addClass('active');
            }

            e.preventDefault();
            return false;
        });
        user_button.click(function(e) {
            if (!user_button.hasClass('active')) {
                user_input.slideDown();
                guest_button.removeClass('active');
                user_button.addClass('active');
            }

            e.preventDefault();
            return false;
        });


        // Date

        $('input[type="datetime"]').datepicker();
    }(window, jQuery));
</script>
