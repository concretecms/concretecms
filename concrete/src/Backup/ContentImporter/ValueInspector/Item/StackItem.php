<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

use Concrete\Core\Page\Stack\Stack;

class StackItem extends AbstractItem
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getDisplayName()
     */
    public function getDisplayName()
    {
        return t('Stack');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getContentObject()
     *
     * @return \Concrete\Core\Page\Stack\Stack|null
     */
    public function getContentObject()
    {
        $reference = $this->getReference();
        $stack = Stack::getByPath($reference);
        if (!$stack) {
            $stack = Stack::getByName($reference);
        }

        return $stack ?: null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getContentValue()
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\AbstractItem::getContentValue()
     *
     * @return string|null the name of the stack
     */
    public function getContentValue()
    {
        $stack = $this->getContentObject();

        return $stack ? $stack->getCollectionName() : null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getFieldValue()
     *
     * @return int|null the ID of the stack
     */
    public function getFieldValue()
    {
        $stack = $this->getContentObject();

        return $stack ? $stack->getCollectionID() : null;
    }
}
