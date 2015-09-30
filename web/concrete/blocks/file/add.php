<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$al = Loader::helper('concrete/asset_library');
?>
<div class="form-group">
	<?=$form->label('fID', t('File'))?>
	<?=$al->file('ccm-b-file', 'fID', t('Choose File'));?>
</div>
<div class="form-group">
	<?=$form->label('fileLinkText', t('Link'))?>
	<?=$form->text('fileLinkText')?>
</div>
<div class="form-group">
	<?=$form->checkbox('forceDownload', '1'); ?>
	<?=$form->label('forceDownload', t('Force file to download')); ?>
</div>
