<?php defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
print $form->textarea($editor->getConversationEditorInputName(),array('class'=>'unbound redactor_conversation_editor_'.$editor->cnvObject->cnvID));
?>
<script>
var textarea = $('textarea.unbound.redactor_conversation_editor_<?=$editor->cnvObject->cnvID?>').removeClass('unbound');
console.log(textarea);
$(textarea).redactor({
    focus: true,
    autoresize: false,
    callback: function(obj) {
        ccm_event.publish('conversationsRedactorEditorLoaded',obj);
        ccm_event.bind('conversationSubmitForm',function(){
            obj.setCode("");
        });
    }
});
</script>