<?php
namespace Concrete\Core\File\Search\ColumnSet;

use Concrete\Core\File\Search\ColumnSet\Column\FileIDColumn;
use Concrete\Core\File\Search\ColumnSet\Column\FileVersionFilenameColumn;
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

    public function __construct()
    {
        parent::__construct();
        $this->addColumn(new Column('authorName', t('Author'),
            [self::class, 'getAuthorName'], false));
        $this->addColumn(new FileIDColumn());
        $this->addColumn(new FileVersionFilenameColumn());
    }
}
