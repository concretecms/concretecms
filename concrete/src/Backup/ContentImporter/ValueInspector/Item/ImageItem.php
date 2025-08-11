<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

class ImageItem extends FileItem
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getDisplayName()
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\FileItem::getDisplayName()
     */
    public function getDisplayName()
    {
        return t('Image');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getContentValue()
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\FileItem::getContentValue()
     *
     * @return string|null
     */
    public function getContentValue()
    {
        $file = $this->getContentObject();
        if ($file === null) {
            return null;
        }
        $uuid = $file->getFileUUID();

        return sprintf('{CCM:FID_%s}', $uuid ?: $file->getFileID());
    }
}
