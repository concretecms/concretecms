<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

use Concrete\Core\Page\Feed;
use Concrete\Core\Page\Stack\Stack;

class StackItem extends AbstractItem
{
    public function getDisplayName()
    {
        return t('Stack');
    }

    public function getContentObject()
    {
        $stack = Stack::getByPath($this->getReference());
        if (!is_object($stack)) {
            // this is kinda weak.
            $stack = Stack::getByName($this->getReference());
        }
        return $stack;
    }

    public function getContentValue()
    {
        if ($o = $this->getContentObject()) {
            return $o->getCollectionName();
        }
    }

    public function getFieldValue()
    {
        if ($o = $this->getContentObject()) {
            return $o->getCollectionID();
        }
    }
}
