<?php
namespace Concrete\Core\Page\Search\ColumnSet;

use Concrete\Core\Page\Search\ColumnSet\Column\PageIDColumn;
use Concrete\Core\Page\Search\ColumnSet\Column\SitemapDisplayOrderColumn;
use Concrete\Core\Page\Search\ColumnSet\Column\UrlPathColumn;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Support\Facade\Application;

class Available extends DefaultSet
{
    protected $attributeClass = 'CollectionAttributeKey';

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
        $this->addColumn(new SitemapDisplayOrderColumn());
        $this->addColumn(new PageIDColumn());
        $this->addColumn(new UrlPathColumn());
        parent::__construct();
        $this->addColumn(new Column('cvStatus', t('Version Status'), ['\Concrete\Core\Page\Search\ColumnSet\Available', 'getCollectionVersionStatus'], false));
    }
}
