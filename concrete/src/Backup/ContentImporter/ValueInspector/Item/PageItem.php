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
        if (preg_match('/^(?:(?<path>[^:]*):)?id=(?<id>[1-9]\d*)$/', $reference, $m)) {
            $path = $m['path'] ?? '';
            $id = (int) $m['id'];
        } else {
            $path = $reference;
            $id = null;
        }
        if ($path === '/' || ($path === '' && $id === null)) {
            $page = Page::getByID(Page::getHomePageID(), 'ACTIVE');
        } elseif ($path !== '') {
            $page = Page::getByPath($path, 'ACTIVE');
        } elseif ($id !== null) {
            $page = Page::getByID($id, 'ACTIVE');
        } else {
            $page = null;
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
