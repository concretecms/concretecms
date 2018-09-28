<?php
namespace Concrete\Core\File\Search\ColumnSet;

use Concrete\Core\File\Search\ColumnSet\Column\FileVersionDateAddedColumn;
use Concrete\Core\File\Search\ColumnSet\Column\FileVersionSizeColumn;
use Concrete\Core\File\Search\ColumnSet\Column\FileVersionTitleColumn;
use Core;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\Set;

class DefaultSet extends ColumnSet
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
        $this->addColumn(new FileVersionTitleColumn());
        $this->addColumn(new Column('fv.fvType', t('Type'), 'getType', false));
        $this->addColumn(new FileVersionDateAddedColumn());
        $this->addColumn(new FileVersionSizeColumn());
        $title = $this->getColumnByKey('fv.fvDateAdded');
        $this->setDefaultSortColumn($title, 'desc');
    }
}
