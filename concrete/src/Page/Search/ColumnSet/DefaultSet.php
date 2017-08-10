<?php
namespace Concrete\Core\Page\Search\ColumnSet;

use Concrete\Core\Page\Search\ColumnSet\Column\CollectionVersionColumn;
use Concrete\Core\Page\Search\ColumnSet\Column\DateLastModifiedColumn;
use Concrete\Core\Page\Search\ColumnSet\Column\DatePublicColumn;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\Set;
use UserInfo;
use Core;

class DefaultSet extends ColumnSet
{
    protected $attributeClass = 'CollectionAttributeKey';

    public static function getCollectionDatePublic($c)
    {
        return Core::make('helper/date')->formatDateTime($c->getCollectionDatePublic());
    }

    public static function getCollectionDateModified($c)
    {
        return Core::make('helper/date')->formatDateTime($c->getCollectionDateLastModified());
    }

    public static function getCollectionAuthor($c)
    {
        $uID = $c->getCollectionUserID();
        $ui = UserInfo::getByID($uID);
        if (is_object($ui)) {
            return $ui->getUserName();
        }
    }

    public function __construct()
    {
        $this->addColumn(new Column('pt.ptHandle', t('Type'), 'getPageTypeName', false));
        $this->addColumn(new CollectionVersionColumn());
        $this->addColumn(new DatePublicColumn());
        $this->addColumn(new DateLastModifiedColumn());
        $this->addColumn(new Column('author', t('Author'), array('\Concrete\Core\Page\Search\ColumnSet\DefaultSet', 'getCollectionAuthor'), false));
        $date = $this->getColumnByKey('c.cDateModified');
        $this->setDefaultSortColumn($date, 'desc');
    }
}
