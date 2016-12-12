<?php defined('C5_EXECUTE') or die("Access Denied.");
$fp = FilePermissions::getGlobal();
$tp = new TaskPermission();
$form = Loader::helper('form');
$cnvID = 0;
$obj = $editor->getConversationObject();
if (is_object($obj)) {
    $cnvID = $obj->getConversationID();
}

print $form->textarea($editor->getConversationEditorInputName(), $editor->getConversationEditorMessageBody(), array('class'=>'unbound conversation-editor redactor_conversation_editor_'.$cnvID));
?>
<script>
$(function() {
    var textarea = $('textarea.unbound.redactor_conversation_editor_<?=$cnvID?>').removeClass('unbound');
    $(textarea).redactor({
        autoresize: false,
        minHeight: '150px',
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
