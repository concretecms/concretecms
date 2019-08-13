<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

/**
 * @since 5.7.5.4
 */
class ImageItem extends FileItem
{
    public function getDisplayName()
    {
        return t('Image');
    }

    public function getContentValue()
    {
        if ($o = $this->getContentObject()) {
            return sprintf("{CCM:FID_%s}", $o->getFileID());
        }
    }
}
