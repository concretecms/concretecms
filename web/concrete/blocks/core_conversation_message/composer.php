<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-discussion-form">
<div class="ccm-conversation-errors alert alert-error"></div>
<div class="control-group">
	<?=$form->label($this->field('cnvMessageSubject'), t('Subject'))?>
	<div class="controls">
		<?=$form->text($this->field('cnvMessageSubject'));?>
	</div>
</div>

<div class="control-group">
	<?=$form->label($this->field('cnvMessageBody'), t('Message'))?>
	<div class="controls">
	<?
	$editor = ConversationEditor::getActive();
	$editor->setConversationEditorInputName($this->field('cnvMessageBody'));
	$editor->outputConversationEditorAddMessageForm(); ?>
	</div>
</div>


</div>