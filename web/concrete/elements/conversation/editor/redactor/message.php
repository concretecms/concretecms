<?php defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
print $form->textarea($editor->getConversationEditorInputName(),array('class'=>'unbound redactor_conversation_editor_'.$editor->cnvObject->cnvID));
?>
<script>
var textarea = $('textarea.unbound.redactor_conversation_editor_<?=$editor->cnvObject->cnvID?>').removeClass('unbound');
$(textarea).redactor({
    focus: true,
    autoresize: false,
    buttons: [ 'bold','italic','deleted','|','fontcolor','|','link' ],
    callback: function(obj) {
        ccm_event.publish('conversationsRedactorEditorLoaded',obj);
        ccm_event.bind('conversationSubmitForm',function(){
            obj.setCode("");
			$('.preview.processing').each(function(){ 
				$('input[rel="'+ $(this).attr('rel') +'"]').remove();
				$(this).remove();
			});
			if($('.attachmentContainer').is(':visible')) {
				$('.attachmentContainer').toggle();
			}
        });
    }
});
</script>