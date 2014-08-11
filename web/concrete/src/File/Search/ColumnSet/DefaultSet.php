<?php
namespace Concrete\Core\File\Search\ColumnSet;

use Core;
use \Concrete\Core\Search\Column\Column;
use \Concrete\Core\Search\Column\Set;

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

        return Core::make('helper/date')->formatDateTime($f->getDateAdded()->getTimestamp());
    }

    public function __construct()
    {
        $this->addColumn(new Column('fv.fvType', t('Type'), 'getType', false));
        $this->addColumn(new Column('fv.fvTitle', t('Title'), 'getTitle'));
        $this->addColumn(new Column('f.fDateAdded', t('Added'), array('\Concrete\Core\File\Search\ColumnSet\DefaultSet', 'getFileDateAdded')));
        $this->addColumn(new Column('fv.fvDateAdded', t('Active'), array('\Concrete\Core\File\Search\ColumnSet\DefaultSet', 'getFileDateActivated')));
        $this->addColumn(new Column('fv.fvSize', t('Size'), 'getSize'));
        $title = $this->getColumnByKey('f.fDateAdded');
        $this->setDefaultSortColumn($title, 'desc');
    }
}
