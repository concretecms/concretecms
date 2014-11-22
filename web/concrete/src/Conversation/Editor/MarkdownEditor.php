<?php
namespace Concrete\Core\Conversation\Editor;

use \Michelf\Markdown;
use Core;

class MarkdownEditor extends Editor
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
        $md = Markdown::defaultTransform(Core::make('helper/text')->entities($cnvMessageBody));
        $formatted = str_replace(array('&amp;lt', '&amp;gt'), array('&lt', '&gt'), $md);
        return parent::formatConversationMessageBody($cnv, $formatted, $config);
    }
}