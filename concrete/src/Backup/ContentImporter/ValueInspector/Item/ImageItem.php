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
            if ($o->getFileUUID()) {
                $identifier = $o->getFileUUID();
            } else {
                $identifier = $o->getFileID();
            }
            return sprintf("{CCM:FID_%s}", $identifier);
        }
    }

}
