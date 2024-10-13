<?php
namespace Concrete\Core\File\Search\ColumnSet;

use Concrete\Core\File\Search\ColumnSet\Column\DownloadsColumn;
use Concrete\Core\File\Search\ColumnSet\Column\FileIDColumn;
use Concrete\Core\File\Search\ColumnSet\Column\FileVersionDateAddedColumn;
use Concrete\Core\File\Search\ColumnSet\Column\FileVersionFilenameColumn;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Search\Column\Column;

class Available extends DefaultSet
{
    public function getAuthorName($node)
    {
        if ($node->getTreeNodeTypeHandle() == 'file_folder') {
            return '';
        }
        if ($node->getTreeNodeTypeHandle() == 'file') {
            $file = $node->getTreeNodeFileObject();
            if (is_object($file)) {
                return $file->getAuthorName();
            }
        }
    }

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
                    return $file->getGenericTypeText(true);
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

    public function __construct()
    {
        parent::__construct();
        $this->addColumn(new Column('authorName', t('Author'),
            [self::class, 'getAuthorName'], false));
        $this->addColumn(new FileIDColumn());
        $this->addColumn(new FileVersionFilenameColumn());
        $this->addColumn(new FileVersionDateAddedColumn());
        $this->addColumn(new DownloadsColumn());
    }
}
