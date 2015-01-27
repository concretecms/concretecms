<?php
$fdh = Core::make('helper/form/date_time'); /* @var $fdh \Concrete\Core\Form\Service\Widget\DateTime */
?>
<section>
    <header>
        <a href="" data-panel-navigation="back" class="ccm-panel-back">
            <span class="fa fa-chevron-left"></span>
        </a>
        <a href="" data-panel-navigation="back">
          <?=t('View as User')?>
        </a>
    </header>
    <form class="preview-panel-form">
        <div class="ccm-panel-content-inner" id="ccm-menu-page-attributes-list">

            <h5><?= t('Date / time') ?></h5>
            <div class="form-group">
            	<?php echo $fdh->datetime('preview_as_user_datetime'); ?>
            </div>

            <h5><?= t('View As') ?></h5>
            <div class="form-group">
                <div class="btn-group">
                    <button class="guest-button btn btn-default active"><?= t('Guest') ?></button>
                    <button class="user-button btn btn-default"><?= t('Site User') ?></button>
                </div>
                <div class="site-user" style="display:none">
                    <label for="user"><?= t('Username') ?></label>
                    <input class="form-control input-sm custom-user" type="text" name="user" />
                </div>
            </div>
            
            <h5><?= t('Emulate Mobile') ?></h5>
            <div class="form-group">
                <div class="btn-group">
                    <button class="disable-mobile-button btn btn-default active"><?= t('Disable') ?></button>
                    <button class="enable-mobile-button btn btn-default"><?= t('Enable') ?></button>
                </div>
                <div class="resolution" style="display:none">
                    <label for="resolution-width"><?= t('Width') ?></label>
                    <div class="input-group">
                        <input class="form-control input-sm resolution-width" type="text" name="resolution-width" />
                        <div class="input-group-addon"><?= t('px') ?></div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</section>

<script>
(function($) {
	$(function() {
		var user_input = $('div.site-user'),
			guest_button = $('button.guest-button'),
			user_button = $('button.user-button'),
			disable_mobile_button = $('button.disable-mobile-button'),
			enable_mobile_button = $('button.enable-mobile-button'),
			resolution_input = $('div.resolution');
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
		// mobile
		disable_mobile_button.click(function(e) {
			if (!disable_mobile_button.hasClass('active')) {
				resolution_input.slideUp();
				enable_mobile_button.removeClass('active');
				disable_mobile_button.addClass('active');
			}
			e.preventDefault();
			return false;
		});
		enable_mobile_button.click(function(e) {
			if (!enable_mobile_button.hasClass('active')) {
				resolution_input.slideDown();
				disable_mobile_button.removeClass('active');
				enable_mobile_button.addClass('active');
			}
			e.preventDefault();
			return false;
		});

	});
}(jQuery));
</script>
