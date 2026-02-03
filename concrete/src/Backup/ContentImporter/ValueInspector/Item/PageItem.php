<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

use Concrete\Core\Page\Page;

class PageItem extends AbstractItem
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getDisplayName()
     */
    public function getDisplayName()
    {
        return t('Page');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getContentObject()
     *
     * @return \Concrete\Core\Page\Page|null
     */
    public function getContentObject()
    {
        $reference = (string) $this->getReference();
        if ($reference === '' || $reference === '/') {
            $page = Page::getByID(Page::getHomePageID(), 'ACTIVE');
        } else {
            $page = Page::getByPath($reference, 'ACTIVE');
        }

        return $page && !$page->isError() ? $page : null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getContentValue()
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\AbstractItem::getContentValue()
     *
     * @return string|null
     */
    public function getContentValue()
    {
        $page = $this->getContentObject();

        return $page ? sprintf('{CCM:CID_%s}', $page->getCollectionID()) : null;
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
        $page = $this->getContentObject();

        return $page ? $page->getCollectionID() : null;
    }
}
