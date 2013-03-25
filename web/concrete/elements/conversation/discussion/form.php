<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
?>

<div class="ccm-discussion-form">

<form data-form="discussion-form" method="post">

<div class="ccm-conversation-errors alert alert-error"></div>

<div class="control-group">
	<?=$form->label('cnvDiscussionSubject', t('Subject'))?>
	<div class="controls">
		<?=$form->text('cnvDiscussionSubject')?>
	</div>
</div>

<div class="control-group">
	<?=$form->label('cnvMessageBody', t('Message'))?>
	<div class="controls">
	<?
	$editor = ConversationEditor::getActive();
	$editor->outputConversationEditorAddMessageForm(); ?>
	</div>
</div>

<div class="control-group">
	<a class="ccm-conversation-attachment-toggle" href="#"><?php echo t('Attach Files'); ?></a>
</div>

</form>

<div class="ccm-conversation-attachment-container">
	<form action="<?php echo Loader::helper('concrete/urls')->getToolsURL('conversations/add_file');?>" class="dropzone" id="file-upload-reply">
	</form>
</div>

</div>