<?php
/**
 * @var $editor \Concrete\Core\Conversation\Editor\RichTextEditor
 * @var $ck \Concrete\Core\Editor\CkeditorEditor
 */
$ck = Core::make('editor');
echo $ck->outputSimpleEditor($editor->getConversationEditorInputName(), $editor->getConversationEditorMessageBody());

?>
<script>
    (function($){
        ConcreteEvent.bind('ConversationSubmitForm',function(){
            Object.keys(CKEDITOR.instances).forEach(function(key) {
                CKEDITOR.instances[key].setData('');
            });
            $('.preview.processing').each(function(){
                $('input[rel="'+ $(this).attr('rel') +'"]').remove();
                $(this).remove();
            });
            if($('.ccm-conversation-attachment-container').is(':visible')) {
                $('.ccm-conversation-attachment-container').toggle();
            }
        });
    })(jQuery)
</script>
