<?php
$fdh = Core::make('helper/form/date_time'); /* @var $fdh \Concrete\Core\Form\Service\Widget\DateTime */
$dh = Core::make('helper/date');
$currentTime = $dh->formatCustom('Y-m-d H:i:s');
?>

<section>
    <header>
        <a href="" data-panel-navigation="back" class="ccm-panel-back">
            <svg><use xlink:href="#icon-arrow-left" /></svg>
            <?= t('Page Settings') ?>
        </a>
        <h5><?= t('View as User') ?></h5>
    </header>
    <form class="preview-panel-form">
        <div class="ccm-panel-content-inner" id="ccm-menu-page-attributes-list">
            <div class="mb-3">
                <label class="form-label" for="date"><?php echo t('Date'); ?></label>
                <input type="date" id="preview-page-as-user-date" value="" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label" for="time"><?php echo t('Time'); ?></label>
                <input type="time" id="preview-page-as-user-time"  value="" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label"><?php echo t('View As'); ?></label>
                <div>
                    <div class="btn-group btn-group-sm">
                        <button class="guest-button btn btn-secondary active"><?php echo t('Guest'); ?></button>
                        <button class="user-button btn btn-secondary"><?php echo t('Site User'); ?></button>
                    </div>
                </div>
            </div>
            <div class="mb-3 site-user" style="display:none">
                <label class="col-form-label"><?php echo t('User'); ?></label>
                <?=$userSelector->quickSelect('custom-user')?>
            </div>
            <div class="mt-5 d-grid">
                <button type="button" class="btn btn-primary" data-button="preview-page-as-user"><?=t('Preview')?></button>
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
