<?php
$fdh = Core::make('helper/form/date_time'); /* @var $fdh \Concrete\Core\Form\Service\Widget\DateTime */
$dh = Core::make('helper/date');
$currentTime = $dh->formatCustom('Y-m-d H:i:s');
?>

<section>
    <header>
        <a href="" data-panel-navigation="back" class="ccm-panel-back">
            <span class="fa fa-chevron-left"></span>
        </a>
        <a href="" data-panel-navigation="back">
          <?php echo t('View as User'); ?>
        </a>
    </header>
    <form class="preview-panel-form">
        <div class="ccm-panel-content-inner" id="ccm-menu-page-attributes-list">
            <h5><?php echo t('Date / Time'); ?></h5>
            <div id="ccm-view-as-user-wrapper">
                <div class="form-group">
                    <?php echo $fdh->datetime('preview_as_user_datetime', $currentTime, false, true, 'dark-panel-calendar'); ?>
                </div>
            </div>
            <br>
            <h5><?php echo t('View As'); ?></h5>
            <div class="btn-group">
                <button class="guest-button btn btn-default active"><?php echo t('Guest'); ?></button>
                <button class="user-button btn btn-default"><?php echo t('Site User'); ?></button>
            </div>
            <br>
            <br>
            <div class="site-user" style="display:none">
                <h5><?php echo t('User'); ?></h5>
                <input class="form-control custom-user" name="user" />
            </div>
        </div>
    </form>
</section>

<script>
(function($) {
    $(function() {
        var user_input = $('div.site-user'),
            guest_button = $('button.guest-button'),
            user_button = $('button.user-button');
        // user
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
    });
}(jQuery));
</script>
