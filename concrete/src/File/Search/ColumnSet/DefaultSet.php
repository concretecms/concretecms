<?php
namespace Concrete\Core\File\Search\ColumnSet;

use Concrete\Core\File\Search\ColumnSet\Column\DateModifiedColumn;
use Concrete\Core\File\Search\ColumnSet\Column\NameColumn;
use Concrete\Core\File\Search\ColumnSet\Column\SizeColumn;
use Concrete\Core\File\Search\ColumnSet\Column\TypeColumn;
use Core;

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
        $app = Application::getFacadeApplication();

        return $app->make('date')->formatDateTime($node->getDateLastModified());
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
        $app = Application::getFacadeApplication();

        return $app->make('date')->formatDateTime($f->getDateAdded()->getTimestamp());
    }

    public function __construct()
    {
        $this->addColumn(new NameColumn());
        $this->addColumn(new TypeColumn());
        $this->addColumn(new DateModifiedColumn());
        $this->addColumn(new SizeColumn());
        $type = $this->getColumnByKey('type');
        $this->setDefaultSortColumn($type, 'asc');
    }
}
