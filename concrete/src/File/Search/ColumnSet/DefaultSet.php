<?php
namespace Concrete\Core\File\Search\ColumnSet;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\File\Search\ColumnSet\Column\DateModifiedColumn;
use Concrete\Core\File\Search\ColumnSet\Column\NameColumn;
use Concrete\Core\File\Search\ColumnSet\Column\SizeColumn;
use Concrete\Core\File\Search\ColumnSet\Column\TypeColumn;

class DefaultSet extends ColumnSet
{
    public static function getType($node)
    {
        switch ($node->getTreeNodeTypeHandle()) {
            case 'file_folder':
                return t('Folder');
            case 'search_preset':
                return t('Saved Search');
            case 'file':
                $file = $node->getTreeNodeFileObject();
                if (is_object($file)) {
                    $type = $file->getTypeObject();
                    if (is_object($type)) {
                        return $type->getGenericDisplayType();
                    } else {
                        return t('Unknown');
                    }
                }
                break;
        }
    }

    public static function getDateModified($node)
    {
        return app('date')->formatDateTime($node->getDateLastModified());
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
        return app('date')->formatDateTime($f->getDateAdded()->getTimestamp());
    }

    public function __construct()
    {
        $this->addColumn(new NameColumn());
        $this->addColumn(new TypeColumn());
        $this->addColumn(new DateModifiedColumn());
        $this->addColumn(new SizeColumn());

        $config = app(Repository::class);

        $type = $this->getColumnByKey($config->get('concrete.file_manager.sort_column') ?: 'name');
        $this->setDefaultSortColumn($type, $config->get('concrete.file_manager.sort_direction') ?: 'asc');
    }

    public static function getDownloads($node)
    {
        if ($node->getTreeNodeTypeHandle() == 'file') {
            $file = $node->getTreeNodeFileObject();
            if (is_object($file)) {
                return $file->getTotalDownloads();
            }
        }

        return '';
    }
}
