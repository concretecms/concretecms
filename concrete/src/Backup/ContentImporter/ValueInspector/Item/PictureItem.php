<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

class PictureItem extends FileItem
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getDisplayName()
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\FileItem::getDisplayName()
     */
    public function getDisplayName()
    {
        return t('Picture');
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

        return $file ? "<concrete-picture fID=\"{$file->getFileID()}\" />" : null;
    }
}
