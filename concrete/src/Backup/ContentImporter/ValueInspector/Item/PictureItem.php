<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

/**
 * @since 5.7.5.3
 */
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
