<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

use Concrete\Core\File\File;

class FileItem extends AbstractItem
{

    public function getDisplayName()
    {
        return t('File');
    }

    public function getContentObject()
    {
        $db = \Database::connection();
        $fID = $db->GetOne('select fID from FileVersions where fvFilename = ?', array($this->getReference()));
        if ($fID) {
            $f = File::getByID($fID);
            return $f;
        }
    }

    public function getContentValue()
    {
        if ($o = $this->getContentObject()) {
            return sprintf("{CCM:FID_DL_%s}", $o->getFileID());
        }
    }

    public function getFieldValue()
    {
        if ($o = $this->getContentObject()) {
            return $o->getFileID();
        }
    }


}