<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php

$helperFile = Loader::helper('concrete/file');
if ($controller->getTask() == 'add') {
    $enablePosting = 1;
    $paginate = 1;
    $itemsPerPage = 50;
    $displayMode = 'threaded';
    $enableOrdering = 1;
    $enableCommentRating = 1;
    $displayPostingForm = 'top';
    $addMessageLabel = t('Add Message');
    $attachmentOverridesEnabled = 0;
    $attachmentsEnabled = 1;
    $fileAccessFileTypes = Config::get('conversations.files.allowed_types');
    //is nothing's been defined, display the constant value
    if (!$fileAccessFileTypes) {
        $fileAccessFileTypes = $helperFile->unserializeUploadFileExtensions(Config::get('concrete.upload.extensions'));
    } else {
        $fileAccessFileTypes = $helperFile->unserializeUploadFileExtensions($fileAccessFileTypes);
    }
    $maxFileSizeGuest = Config::get('conversations.files.guest.max_size');
    $maxFileSizeRegistered = Config::get('conversations.files.registered.max_size');
    $maxFilesGuest = Config::get('conversations.files.guest.max');
    $maxFilesRegistered = Config::get('conversations.files.registered.max');
    $fileExtensions = implode(',', $fileAccessFileTypes);
    $attachmentsEnabled = intval(Config::get('conversations.attachments_enabled'));
    $notificationUsers = Conversation::getDefaultSubscribedUsers();
    $subscriptionEnabled = intval(Config::get('conversations.subscription_enabled'));
}

if (!$dateFormat) {
    $dateFormat = 'default';
}
?>

<fieldset>
	<legend><?=t('Message List')?></legend>
	<div class="form-group">
		<label class="control-label"><?=t('Display Mode')?></label>
		<div class="radio">
			<label>
			<?=$form->radio('displayMode', 'threaded', $displayMode)?>
			<?=t('Threaded')?>
			</label>
		</div>
		<div class="radio">
			<label>
			<?=$form->radio('displayMode', 'flat', $displayMode)?>
			<?=t('Flat')?>
			</label>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label"><?=t('Ordering')?></label>
		<?=$form->select('orderBy', array('date_asc' => t('Earliest First'), 'date_desc' => t('Most Recent First'), 'rating' => t('Highest Rated')), $orderBy)?>

		<div class="checkbox">
			<label>
				<?=$form->checkbox('enableOrdering', 1, $enableOrdering)?>
				<?=t('Display Ordering Option in Page')?>
			</label>
		</div>

	</div>
	<div class="form-group">
		<label class="control-label"><?=t('Rating')?></label>
		<div class="checkbox">
			<label>
			<?=$form->checkbox('enableCommentRating', 1, $enableCommentRating)?>
			<?=t('Enable Comment Rating')?>
			</label>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label"><?=t('Paginate Message List')?></label>
		<div class="radio">
			<label>
			<?=$form->radio('paginate', 0, $paginate)?>
			<?=t('No, display all messages.')?>
			</label>
		</div>
		<div class="radio">
			<label>
			<?=$form->radio('paginate', 1, $paginate)?>
			<?=t('Yes, display only a sub-set of messages at a time.')?>
			</label>
		</div>
	</div>
	<div class="form-group" data-row="itemsPerPage">
		<label class="control-label"><?=t('Messages Per Page')?></label>
		<?=$form->text('itemsPerPage', $itemsPerPage, array('class' => 'span1'))?>
	</div>
</fieldset>

<fieldset>
	<legend><?=t('Posting')?></legend>
	<div class="form-group">
		<?=$form->label('addMessageLabel', t('Add Message Label'))?>
		<?=$form->text('addMessageLabel', $addMessageLabel)?>
	</div>
	<div class="form-group">
		<label class="control-label"><?=t('Enable Posting')?></label>
		<div class="radio">
			<label>
			<?=$form->radio('enablePosting', 1, $enablePosting)?>
			<span><?=t('Yes, this conversation accepts messages and replies.')?></span>
			</label>
		</div>
		<div class="radio">
			<label>
			<?=$form->radio('enablePosting', 0, $enablePosting)?>
			<span><?=t('No, posting is disabled.')?></span>
			</label>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label"><?=t('Display Posting Form')?></label>
		<div class="radio">
			<label>
			<?=$form->radio('displayPostingForm', 'top', $displayPostingForm)?>
			<span><?=t('Top')?></span>
			</label>
		</div>
		<div class="radio">
			<label>
			<?=$form->radio('displayPostingForm', 'bottom', $displayPostingForm)?>
			<span><?=t('Bottom')?></span>
			</label>
		</div>
	</div>
</fieldset>
<fieldset>
	<div class="form-group">
		<label class="control-label"><?=t('Date Format')?></label>
		<div class="radio">
			<label>
			<?=$form->radio('dateFormat', 'default', $dateFormat)?>
			<span><?=t('Use Site Default.')?></span>
			</label>
		</div>
		<div class="radio">
			<label>
			<?=$form->radio('dateFormat', 'elapsed', $dateFormat)?>
			<span><?=t('Time elapsed since post.')?></span>
			</label>
		</div>
		<div class="radio">
			<label>
			<?=$form->radio('dateFormat', 'custom', $dateFormat)?>
			<span><?=t('Custom')?></span>
			</label>
		</div>
		<?=$form->text('customDateFormat', $customDateFormat)?>
	</div>
</fieldset>
<fieldset>
	<legend><?=t('File Attachment Management')?></legend>
	<p class="text-muted"><?php echo t('Note: Entering values here will override global conversations file attachment settings for this block if you enable Attachment Overrides for this Conversation.') ?></p>
    <div class="form-group">
        <div class="checkbox">
            <label class="">
            <?=$form->checkbox('attachmentOverridesEnabled', 1, $attachmentOverridesEnabled)?><?=t('Enable Attachment Overrides')?>
            </label>
        </div>
		<div class="attachment-overrides">
			<div class="checkbox">
				<label class="">
				<?=$form->checkbox('attachmentsEnabled', 1, $attachmentsEnabled)?><?=t('Enable Attachments')?>
				</label>
			</div>
		</div>
	</div>
    <div class="form-group attachment-overrides">
		<label class="control-label"><?=t('Max Attachment Size for Guest Users. (MB)')?></label>
		<div class="controls">
			<?=$form->text('maxFileSizeGuest', $maxFileSizeGuest > 0 ? $maxFileSizeGuest : '')?>
		</div>
	</div>
	<div class="form-group attachment-overrides">
		<label class="control-label"><?=t('Max Attachment Size for Registered Users. (MB)')?></label>
		<div class="controls">
			<?=$form->text('maxFileSizeRegistered', $maxFileSizeRegistered > 0 ? $maxFileSizeRegistered : '')?>
		</div>
	</div>
	<div class="form-group attachment-overrides">
		<label class="control-label"><?=t('Max Attachments Per Message for Guest Users.')?></label>
		<div class="controls">
			<?=$form->text('maxFilesGuest', $maxFilesGuest > 0 ? $maxFilesGuest : '')?>
		</div>
	</div>
	<div class="form-group attachment-overrides">
		<label class="control-label"><?=t('Max Attachments Per Message for Registered Users')?></label>
		<div class="controls">
			<?=$form->text('maxFilesRegistered', $maxFilesRegistered > 0 ?  $maxFilesRegistered : '')?>
		</div>
	</div>
	<div class="form-group attachment-overrides">
		<label class="control-label"><?=t('Allowed File Extensions (Comma separated, no periods).')?></label>
		<div class="controls">
			<?=$form->textarea('fileExtensions', $fileExtensions)?>
		</div>
	</div>


</fieldset>

<fieldset>
	<legend><?=t('Notification')?></legend>
	<div class="form-group">
		<div class="checkbox">
			<label>
				<?=$form->checkbox('notificationOverridesEnabled', 1, $notificationOverridesEnabled)?><?=t('Override Global Settings')?>
			</label>
		</div>
	</div>
	<div class="form-group notification-overrides">
		<label class="control-label"><?=t('Users To Receive Conversation Notifications')?></label>
		<?=Core::make("helper/form/user_selector")->selectMultipleUsers('notificationUsers', $notificationUsers)?>
	</div>
	<div class="form-group notification-overrides">
		<label class="control-label"><?=t('Subscribe Option')?></label>
		<div class="checkbox">
			<label><?=$form->checkbox('subscriptionEnabled', 1, $subscriptionEnabled)?>
				<?=t('Yes, allow registered users to choose to subscribe to conversations.')?>
			</label>
		</div>
	</div>
</fieldset>

<script type="text/javascript">
$(function() {
	$('input[name=paginate]').on('change', function() {
		var pg = $('input[name=paginate]:checked');
		if (pg.val() == 1) {
			$('div[data-row=itemsPerPage]').show();
		} else {
			$('div[data-row=itemsPerPage]').hide();
		}
	}).trigger('change');
    $('input[name=attachmentOverridesEnabled]').on('change', function() {
        var ao = $('input[name=attachmentOverridesEnabled]:checked');
        if (ao.val() == 1) {
            $('.attachment-overrides input, .attachment-overrides textarea').prop('disabled', false);
            $('.attachment-overrides label').removeClass('text-muted');
        } else {
            $('.attachment-overrides input, .attachment-overrides textarea').prop('disabled', true);
            $('.attachment-overrides label').addClass('text-muted');
        }
    }).trigger('change');
	$('input[name=notificationOverridesEnabled]').on('change', function() {
		var ao = $('input[name=notificationOverridesEnabled]:checked');
		if (ao.val() == 1) {
			$('.notification-overrides').show();
		} else {
			$('.notification-overrides').hide();
		}
	}).trigger('change');
});
</script>
