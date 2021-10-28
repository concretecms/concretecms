<?php


namespace Concrete\Core\Conversation\Editor;

use AssetList;
use Concrete\Core\Editor\EditorInterface;

class RichTextEditor extends Editor
{
    public function getConversationEditorAssetPointers()
    {
        /** @TODO Move CKEditor registration out of Editor Interface Factory */
        \Core::make(EditorInterface::class);
        $list = AssetList::getInstance();

        $r = $list->getAssetGroup('ckeditor');

        return $r->getAssetPointers();
    }

    public function outputConversationEditorReplyMessageForm()
    {
        $this->outputConversationEditorAddMessageForm();
    }

    public function formatConversationMessageBody($cnv, $cnvMessageBody, $config = array())
    {
        $config = array_merge(['htmlawed' => true], $config);
        return parent::formatConversationMessageBody($cnv, $cnvMessageBody, $config);
    }
}