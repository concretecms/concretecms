<?php

namespace Concrete\Core\File\Menu\Item;

use Concrete\Core\Entity\File\File;
use Concrete\Core\Tree\Menu\Item\AbstractItem;

class DeleteFileItem extends AbstractItem
{

    /**
     * @var File
     */
    protected $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function getActionURL()
    {
        return \URL::to('/ccm/system/dialogs/file/delete', $this->file->getFileID());
    }

    public function getAction()
    {
        return 'delete-file';
    }

    public function getItemName()
    {
        return t('Delete File');
    }

    public function getDialogTitle()
    {
        return t('Delete %s', $this->file->getFilename());
    }


}