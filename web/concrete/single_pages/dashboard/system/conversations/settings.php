<?php  defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$file = Loader::helper('file');
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Conversations Settings'), false, 'span8 offset2', false);
?>
<form action="<?=$this->action('save')?>" method='post'>
	<div class='ccm-pane-body'>
		<fieldset>
			<legend><?php echo t('Conversation File Attachment Settings'); ?></legend>
			<p style="margin-bottom: 25px; color: #aaa; display: block;" class="small"><?php echo t('Note: These settings can be overridden in the block edit form for individual conversations.') ?></p>
			<div class="control-group">
				<label class="control-label"><?=t('Max Attachment Size for Guest Users. (MB)')?></label>
				<div class="controls">		
					<?=$form->text('maxFileSizeGuest', $maxFileSizeGuest > 0 ? $maxFileSizeGuest : '')?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?=t('Max Attachment Size for Registered Users. (MB)')?></label>
				<div class="controls">
					<?=$form->text('maxFileSizeRegistered', $maxFileSizeRegistered > 0 ? $maxFileSizeRegistered : '')?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?=t('Max Attachments Per Message for Guest Users.')?></label>
				<div class="controls">
					<?=$form->text('maxFilesGuest', $maxFilesGuest > 0 ? $maxFilesGuest : '')?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?=t('Max Attachments Per Message for Registered Users')?></label>
				<div class="controls">
					<?=$form->text('maxFilesRegistered', $maxFilesRegistered > 0 ?  $maxFilesRegistered : '')?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?=t('Allowed File Extensions (Comma separated, no periods).')?></label>
				<div class="controls">
					<?=$form->textarea('fileExtensions', $fileExtensions)?>
				</div>
			</div>
		</fieldset>
	</div>
	<div class='ccm-pane-footer'>
		<button class='btn btn-primary pull-right'><?php echo t('Save'); ?></button>
	</div>
</form>