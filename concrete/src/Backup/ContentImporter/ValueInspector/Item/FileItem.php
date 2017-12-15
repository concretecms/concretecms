<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

use Concrete\Core\File\File;

class FileItem implements ItemInterface
{

    protected $filename;
    protected $prefix;

    /**
     * FileItem constructor.
     * @param $filename
     * @param $prefix
     */
    public function __construct($filename, $prefix = null)
    {
        $this->filename = $filename;
        $this->prefix = $prefix;
    }

    public function getReference()
    {
        $reference = '';
        if ($this->prefix) {
            $reference = $this->prefix . ':';
        }
        $reference .= $this->filename;
        return $reference;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return null
     */
    public function getPrefix()
    {
        return $this->prefix;
    }


    public function getDisplayName()
    {
        return t('File');
    }

    public function getContentObject()
    {
        $db = \Database::connection();
        $fID = null;
        if ($this->prefix) {
            $fID = $db->GetOne('select fID from FileVersions where fvPrefix = ? and fvFilename = ?', [$this->prefix, $this->filename]);
        } else {
            $fID = $db->GetOne('select fID from FileVersions where fvFilename = ?', [$this->filename]);
        }

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
