<?php
namespace Concrete\Core\Page\Search\ColumnSet;

use \Concrete\Core\Search\Column\Column;
use \Concrete\Core\Search\Column\Set;
use UserInfo;
use Core;

class DefaultSet extends Set
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

    public function getCollectionAuthor($c)
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
        $this->addColumn(new Column('cv.cvName', t('Name'), 'getCollectionName'));
        $this->addColumn(new Column('cv.cvDatePublic', t('Date'), array('\Concrete\Core\Page\Search\ColumnSet\DefaultSet', 'getCollectionDatePublic')));
        $this->addColumn(new Column('c.cDateModified', t('Last Modified'), array('\Concrete\Core\Page\Search\ColumnSet\DefaultSet', 'getCollectionDateModified')));
        $this->addColumn(new Column('author', t('Author'), array('\Concrete\Core\Page\Search\ColumnSet\DefaultSet', 'getCollectionAuthor'), false));
        $date = $this->getColumnByKey('c.cDateModified');
        $this->setDefaultSortColumn($date, 'desc');
    }
}
