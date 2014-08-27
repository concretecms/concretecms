<?php
namespace Concrete\Core\Conversation\Editor;

use Core;

class PlainTextEditor extends Editor
{

    public function getConversationEditorAssetPointers()
    {
        return array();
    }

    public function outputConversationEditorReplyMessageForm()
    {
        $this->outputConversationEditorAddMessageForm();
    }

    public function formatConversationMessageBody($cnv, $cnvMessageBody, $config = array())
    {
        /** @var \Concrete\Core\Utility\Service\Text $text */
        $text = Core::make('helper/text');
        $formatted = nl2br($text->entities($cnvMessageBody));
        return parent::formatConversationMessageBody($cnv, $formatted, $config);
    }
}