<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

use Concrete\Core\Page\Type\Type;

class PageTypeItem extends AbstractItem
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getDisplayName()
     */
    public function getDisplayName()
    {
        return t('Page Type');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getContentObject()
     *
     * @return \Concrete\Core\Page\Type\Type|null
     */
    public function getContentObject()
    {
        $reference = (string) $this->getReference();
        if ($reference === '') {
            return null;
        }
        if (preg_match('/^(?<handle>[^:]*):id=(?<id>[1-9]\d*)$/', $reference, $m)) {
            $handle = $m['handle'];
            $id = (int) $m['id'];
        } else {
            $handle = $reference;
            $id = null;
        }
        if ($handle !== '') {
            return Type::getByHandle($handle);
        }
        if ($id !== null) {
            return Type::getByID($id);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getFieldValue()
     *
     * @return int|null
     */
    public function getFieldValue()
    {
        $pageType = $this->getContentObject();

        return $pageType ? $pageType->getPageTypeID() : null;
    }
}
