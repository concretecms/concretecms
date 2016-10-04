<?php
namespace Concrete\Core\File\Search\ColumnSet;

use Concrete\Core\File\Type\Type;
use Core;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\Set;

class FolderSet extends Set
{
    public static function getType($node)
    {
        if ($node->getTreeNodeTypeHandle() == 'file_folder') {
            return t('Folder');
        }
        if ($node->getTreeNodeTypeHandle() == 'search_preset') {
            return t('Saved Search');
        }
        if ($node->getTreeNodeTypeHandle() == 'file') {
            $file = $node->getTreeNodeFileObject();
            if (is_object($file)) {
                return Type::getGenericTypeText($file->getTypeObject()->getGenericType());
            }
        }
    }

    public static function getDateModified($node)
    {
        return \Core::make('date')->formatDateTime($node->getDateLastModified());
    }

    public static function getName($node)
    {
        return $node->getTreeNodeDisplayName();
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
        $this->addColumn(new Column('folderItemName', t('Name'), ['\Concrete\Core\File\Search\ColumnSet\FolderSet', 'getName']));
        $this->addColumn(new Column('folderItemType', t('Type'), ['\Concrete\Core\File\Search\ColumnSet\FolderSet', 'getType']));
        $this->addColumn(new Column('folderItemModified', t('Date Modified'), ['\Concrete\Core\File\Search\ColumnSet\FolderSet', 'getDateModified']));
        $this->addColumn(new Column('folderItemSize', t('Size'), ['\Concrete\Core\File\Search\ColumnSet\FolderSet', 'getSize']));
        $title = $this->getColumnByKey('folderItemName');
        $this->setDefaultSortColumn($title, 'desc');
    }
}
