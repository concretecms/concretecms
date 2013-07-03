<? defined('C5_EXECUTE') or die("Access Denied."); ?>  
<?

$helperFile = Loader::helper('concrete/file');
if($fileExtensions) {  // format file extensions for viewing and editing. 
	$fileExtensions = $helperFile->unserializeUploadFileExtensions($fileExtensions);
	$fileExtensions = implode(',', $fileExtensions);
}
if ($controller->getTask() == 'add') {
	$enablePosting = 1;
	$paginate = 1;
	$itemsPerPage = 50;
	$displayMode = 'threaded';
	$insertNewMessages = 'top';
	$enableOrdering = 1;
	$enableCommentRating = 1;
	$displayPostingForm = 'top';
	$addMessageLabel = t('Add Message');
}
if(!$dateFormat) {
	$dateFormat = 'default';
}
?>
<div class="form-horizontal">

	<fieldset>
		<legend><?=t('Message List')?></legend>
		<div class="control-group">
			<label class="control-label"><?=t('Display Mode')?></label>
			<div class="controls">
				<label class="radio">
					<?=$form->radio('displayMode', 'threaded', $displayMode)?>
					<span><?=t('Threaded')?></span>
				</label>
				<label class="radio">
					<?=$form->radio('displayMode', 'flat', $displayMode)?>
					<span><?=t('Flat')?></span>
				</label>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?=t('Ordering')?></label>
			<div class="controls">
				<?=$form->select('orderBy', array('date_asc' => 'Earliest First', 'date_desc' => 'Most Recent First', 'rating' => 'Highest Rated'), $orderBy)?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?=t('Display Ordering Option in Page')?></label>
			<div class="controls">
			<?=$form->checkbox('enableOrdering', 1, $enableOrdering)?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?=t('Enable Comment Rating')?></label>
			<div class="controls">
			<?=$form->checkbox('enableCommentRating', 1, $enableCommentRating)?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?=t('Paginate Message List')?></label>
			<div class="controls">
				<label class="radio">
					<?=$form->radio('paginate', 0, $paginate)?>
					<span><?=t('No, display all messages.')?></span>
				</label>
				<label class="radio">
					<?=$form->radio('paginate', 1, $paginate)?>
					<span><?=t('Yes, display only a sub-set of messages at a time.')?></span>
				</label>
			</div> 
		</div>
		<div class="control-group" data-row="itemsPerPage">
			<label class="control-label"><?=t('Messages Per Page')?></label>
			<div class="controls">
				<?=$form->text('itemsPerPage', $itemsPerPage, array('class' => 'span1'))?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?=t('Add new messages')?></label>
			<div class="controls">
				<label class="radio">
					<?=$form->radio('insertNewMessages', 'top', $insertNewMessages)?>
					<span><?=t('Top')?></span>
				</label>
				<label class="radio">
					<?=$form->radio('insertNewMessages', 'bottom', $insertNewMessages)?>
					<span><?=t('Bottom')?></span>
				</label>
			</div>
		</div>
	</fieldset>
	
	<fieldset>
		<legend><?=t('Posting')?></legend>
		<div class="control-group">
			<?=$form->label('addMessageLabel', t('Add Message Label'))?>
			<div class="controls">
				<?=$form->text('addMessageLabel', $addMessageLabel)?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?=t('Enable Posting')?></label>
			<div class="controls">
				<label class="radio">
					<?=$form->radio('enablePosting', 1, $enablePosting)?>
					<span><?=t('Yes, this conversation accepts messages and replies.')?></span>
				</label>
				<label class="radio">
					<?=$form->radio('enablePosting', 0, $enablePosting)?>
					<span><?=t('No, posting is disabled.')?></span>
				</label>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?=t('Display Posting Form')?></label>
			<div class="controls">
				<label class="radio">
					<?=$form->radio('displayPostingForm', 'top', $displayPostingForm)?>
					<span><?=t('Top')?></span>
				</label>
				<label class="radio">
					<?=$form->radio('displayPostingForm', 'bottom', $displayPostingForm)?>
					<span><?=t('Bottom')?></span>
				</label>
			</div>
		</div>
	</fieldset>
	<fieldset>
		<legend><?=t('Date Format')?></legend>
		<div class="control-group">
			<div class="controls">
				<label class="radio">
					<?=$form->radio('dateFormat', 'default', $dateFormat)?>
					<span><?=t('Use Site Default.')?></span>
				</label>
				<label class="radio">
					<?=$form->radio('dateFormat', 'elapsed', $dateFormat)?>
					<span><?=t('Time elapsed since post.')?></span>
				</label>
				<label class="radio">
					<?=$form->radio('dateFormat', 'custom', $dateFormat)?>
					<span><?=t('Custom')?></span>
				</label>
				<?=$form->text('customDateFormat', $customDateFormat)?>
			</div>
		</div>
	</fieldset>
	<fieldset>
		<legend><?=t('File Attachment Management')?></legend>
		<p class="muted"><?php echo t('Note: Entering values here will override global conversations file attachment settings for this block. Leave blank to use global settings.') ?></p>
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
});
</script>
