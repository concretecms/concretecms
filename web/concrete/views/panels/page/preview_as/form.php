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
    <form class="preview-panel-form form-horizontal">
        <div class="ccm-panel-content-inner" id="ccm-menu-page-attributes-list">

            <label class="label"><?= t('Date / time') ?></label>
            <div>
            	<?php echo $fdh->datetime('preview_as_user_datetime'); ?>
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
