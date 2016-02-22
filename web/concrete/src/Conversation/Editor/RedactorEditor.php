<?php
namespace Concrete\Core\Conversation\Editor;

use AssetList;

class RedactorEditor extends Editor
{
    public function getConversationEditorAssetPointers()
    {
        $list = AssetList::getInstance();
        $r = $list->getAssetGroup('redactor');

        return $r->getAssetPointers();
    }

    public function outputConversationEditorReplyMessageForm()
    {
        $this->outputConversationEditorAddMessageForm();
    }

    public function formatConversationMessageBody($cnv, $cnvMessageBody, $config = array('htmlawed' => true))
    {
        return parent::formatConversationMessageBody($cnv, $cnvMessageBody, $config);
    }
}
