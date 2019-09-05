<?php
namespace Concrete\Core\Page\Search\ColumnSet;

use Concrete\Core\Page\Search\ColumnSet\Column\CollectionVersionColumn;
use Concrete\Core\Page\Search\ColumnSet\Column\DateLastModifiedColumn;
use Concrete\Core\Page\Search\ColumnSet\Column\DatePublicColumn;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\Set;
use Concrete\Core\Support\Facade\Application;
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

    /**
     * @see \Concrete\Core\Page\Collection\Version\Version::get()
     *
     * @param \Concrete\Core\Page\Page $c
     *
     * @return string
     */
    public static function getCollectionVersionStatus($c)
    {
        $cvStatus = '';
        $app = Application::getFacadeApplication();
        $now = $app->make('date')->getOverridableNow();

        $vObj = $c->getVersionObject();
        if ($vObj) {
            if ($vObj->isApproved() && (!$vObj->getPublishDate() || $vObj->getPublishDate() <= $now) && (!$vObj->getPublishEndDate() || $vObj->getPublishEndDate() >= $now)) {
                $cvStatus = t('Approved');
            } elseif ($vObj->isApproved() && ($vObj->getPublishDate() && $vObj->getPublishDate() > $now)) {
                $cvStatus = t('Scheduled');
            } elseif (!$vObj->isApproved()) {
                $cvStatus = t('Unapproved');
            }
        }

        return $cvStatus;
    }

    public function __construct()
    {
        $this->addColumn(new Column('pt.ptHandle', t('Type'), 'getPageTypeName', false));
        $this->addColumn(new CollectionVersionColumn());
        $this->addColumn(new DatePublicColumn());
        $this->addColumn(new DateLastModifiedColumn());
        $this->addColumn(new Column('author', t('Author'), array('\Concrete\Core\Page\Search\ColumnSet\DefaultSet', 'getCollectionAuthor'), false));
        $this->addColumn(new Column('cvStatus', t('Version Status'), array('\Concrete\Core\Page\Search\ColumnSet\DefaultSet', 'getCollectionVersionStatus'), false));
        $date = $this->getColumnByKey('c.cDateModified');
        $this->setDefaultSortColumn($date, 'desc');
    }
}
