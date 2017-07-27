<?php
namespace Concrete\Core\File\Search\ColumnSet;

use Concrete\Core\File\Search\ColumnSet\Column\FileID;
use Concrete\Core\File\Search\ColumnSet\Column\FileVersionDateAdded;
use Concrete\Core\File\Search\ColumnSet\Column\FileVersionSize;
use Concrete\Core\File\Search\ColumnSet\Column\FileVersionTitle;
use Core;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\Set;

class DefaultSet extends Set
{
    protected $attributeClass = 'FileAttributeKey';

    public static function getFileDateAdded($f)
    {
        return Core::make('helper/date')->formatDateTime($f->getDateAdded()->getTimestamp());
    }

    public static function getFileDateActivated($f)
    {
        $fv = $f->getVersion();

        return Core::make('helper/date')->formatDateTime($fv->getDateAdded()->getTimestamp());
    }

    public function __construct()
    {
        $this->addColumn(new FileVersionTitle());
        $this->addColumn(new Column('fv.fvType', t('Type'), 'getType', false));
        $this->addColumn(new FileVersionDateAdded());
        $this->addColumn(new FileVersionSize());
        $title = $this->getColumnByKey('fv.fvDateAdded');
        $this->setDefaultSortColumn($title, 'desc');
    }
}
