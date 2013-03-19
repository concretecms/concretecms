<?php defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
print $form->textarea($editor->getConversationEditorInputName(),array('class'=>'unbound plaintext_conversation_editor_'.$editor->cnvObject->cnvID));
?>
<script type="text/javascript">
	var me = $('textarea.unbound.plaintext_conversation_editor_<?=$editor->cnvObject->cnvID?>').removeClass('unbound');
	(function($,window,me){
		ccm_event.bind('conversationSubmitForm',function(){
			me.val('');
		});
	})(jQuery,window,me)
</script>