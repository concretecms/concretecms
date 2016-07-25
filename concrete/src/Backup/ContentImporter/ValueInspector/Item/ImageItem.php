<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

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
