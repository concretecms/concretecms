<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

use Concrete\Core\Page\Feed;

class PageFeedItem extends AbstractItem
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getDisplayName()
     */
    public function getDisplayName()
    {
        return t('RSS Feed');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getContentObject()
     *
     * @return \Concrete\Core\Entity\Page\Feed|null
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
            return Feed::getByHandle($handle);
        }
        if ($id !== null) {
            return Feed::getByID($id);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getContentValue()
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\AbstractItem::getContentValue()
     *
     * @return \League\Url\UrlInterface|null
     */
    public function getContentValue()
    {
        $feed = $this->getContentObject();

        return $feed ? $feed->getFeedURL() : null;
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
        $feed = $this->getContentObject();

        return $feed ? $feed->getID() : null;
    }
}
