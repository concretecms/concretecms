<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

class PictureItem extends FileItem
{
    public function getDisplayName()
    {
        return t('Picture');
    }

    public function getContentValue()
    {
        if ($o = $this->getContentObject()) {
            return '<concrete-picture fID="' . $o->getFileID() . '" />';
        }
    }
}
