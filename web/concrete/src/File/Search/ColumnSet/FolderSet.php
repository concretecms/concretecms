<?php
namespace Concrete\Core\File\Search\ColumnSet;

use Core;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\Set;

class FolderSet extends Set
{
    public static function getType($node)
    {
        return t('Folder');
    }

    public static function getDateModified($node)
    {
        return \Core::make('date')->formatDateTime($node->getDateLastModified());
    }

    public static function getName($node)
    {
        return $node->getTreeNodeDisplayName();
    }

    public static function getThumbnail($node)
    {
        return '<i class="fa fa-folder"></i>';
    }
    public static function getSize($node)
    {
        if ($node->getTreeNodeTypeHandle() == 'file_folder') {
            return '';
        }
        if ($node->getTreeNodeTypeHandle() == 'file') {
            $file = $node->getTreeNodeFileObject();
            if (is_object($file)) {
                return $file->getSize();
            }
        }
    }

    public static function getFileDateActivated($f)
    {
        $fv = $f->getVersion();

        return Core::make('helper/date')->formatDateTime($f->getDateAdded()->getTimestamp());
    }

    public function __construct()
    {
        $this->addColumn(new Column('folderItemThumbnail', '', array('\Concrete\Core\File\Search\ColumnSet\FolderSet', 'getThumbnail'), false));
        $this->addColumn(new Column('folderItemName', t('Name'), array('\Concrete\Core\File\Search\ColumnSet\FolderSet', 'getName')));
        $this->addColumn(new Column('folderItemType', t('Type'), array('\Concrete\Core\File\Search\ColumnSet\FolderSet', 'getType'), false));
        $this->addColumn(new Column('folderItemModified', t('Date Modified'), array('\Concrete\Core\File\Search\ColumnSet\FolderSet', 'getDateModified')));
        $this->addColumn(new Column('folderItemSize', t('Size'), array('\Concrete\Core\File\Search\ColumnSet\FolderSet', 'getSize')));
        $title = $this->getColumnByKey('folderItemName');
        $this->setDefaultSortColumn($title, 'desc');
    }
}


