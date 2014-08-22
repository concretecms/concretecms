<?php defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
print $form->textarea($editor->getConversationEditorInputName(), $editor->getConversationEditorMessageBody(), array('class'=>'unbound conversation-editor redactor_conversation_editor_'.$editor->getConversationObject()->getConversationID()));
?>
<script>
$(function() {
    var textarea = $('textarea.unbound.redactor_conversation_editor_<?=$editor->getConversationObject()->getConversationID()?>').removeClass('unbound');
    $(textarea).redactor({
        autoresize: false,
        buttons: [ 'bold','italic','deleted','|','fontcolor','|','link' ],
        callback: function(obj) {
            ConcreteEvent.publish('ConversationRedactorEditorLoaded',obj);
            ConcreteEvent.bind('ConversationSubmitForm',function(){
                obj.setCode("");
    			$('.preview.processing').each(function(){ 
    				$('input[rel="'+ $(this).attr('rel') +'"]').remove();
    				$(this).remove();
    			});
    			if($('.ccm-conversation-attachment-container').is(':visible')) {
    				$('.ccm-conversation-attachment-container').toggle();
    			}
            });
        }
    });
});
</script>