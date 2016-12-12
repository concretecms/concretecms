<?php
namespace Concrete\Core\Asset\Group;

use Concrete\Core\Asset\AssetGroup as AssetGroup;
use Concrete\Core\Conversation\Editor\Editor;

class CoreConversationAssetGroup extends AssetGroup
{
    public function getAssetPointers()
    {
        $assetPointers = parent::getAssetPointers();
        $editor = Editor::getActive();
        foreach ((array) $editor->getConversationEditorAssetPointers() as $assetPointer) {
            $assetPointers[] = $assetPointer;
        }

        return $assetPointers;
    }
}
