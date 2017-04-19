<?php
namespace Concrete\Core\File\Search\ColumnSet;

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
        $this->addColumn(new Column('fv.fvTitle', t('Name'), 'getTitle'));
        $this->addColumn(new Column('fv.fvType', t('Type'), 'getType', false));
        $this->addColumn(new Column('fv.fvDateAdded', t('Modified'), array('\Concrete\Core\File\Search\ColumnSet\DefaultSet', 'getFileDateActivated')));
        $this->addColumn(new Column('fv.fvSize', t('Size'), 'getSize'));
        $title = $this->getColumnByKey('fv.fvDateAdded');
        $this->setDefaultSortColumn($title, 'desc');
    }
}
