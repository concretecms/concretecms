<?php
namespace Concrete\Core\Page\Search\ColumnSet;

use Concrete\Core\Page\Search\ColumnSet\Column\CollectionVersion;
use Concrete\Core\Page\Search\ColumnSet\Column\DateLastModified;
use Concrete\Core\Page\Search\ColumnSet\Column\DatePublic;
use Concrete\Core\Search\Column\CollectionAttributeKeyColumn;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\Set;
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

    public static function getCollectionAuthor($c)
    {
        $uID = $c->getCollectionUserID();
        $ui = UserInfo::getByID($uID);
        if (is_object($ui)) {
            return $ui->getUserName();
        }
    }

    public function getAttributeKeyColumn($akHandle)
    {
        $ak = call_user_func(array($this->attributeClass, 'getByHandle'), $akHandle);
        $col = new CollectionAttributeKeyColumn($ak);
        return $col;
    }

    public function __construct()
    {
        $this->addColumn(new Column('pt.ptHandle', t('Type'), 'getPageTypeName', false));
        $this->addColumn(new CollectionVersion());
        $this->addColumn(new DatePublic());
        $this->addColumn(new DateLastModified());
        $this->addColumn(new Column('author', t('Author'), array('\Concrete\Core\Page\Search\ColumnSet\DefaultSet', 'getCollectionAuthor'), false));
        $date = $this->getColumnByKey('c.cDateModified');
        $this->setDefaultSortColumn($date, 'desc');
    }
}
