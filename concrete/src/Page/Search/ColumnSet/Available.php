<?php

declare(strict_types=1);

namespace Concrete\Core\Page\Search\ColumnSet;

use Concrete\Core\Page\Search\ColumnSet\Column\PageIDColumn;
use Concrete\Core\Page\Search\ColumnSet\Column\PageTemplateColumn;
use Concrete\Core\Page\Search\ColumnSet\Column\SitemapDisplayOrderColumn;
use Concrete\Core\Page\Search\ColumnSet\Column\UrlPathColumn;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Support\Facade\Application;

defined('C5_EXECUTE') or die('Access Denied.');

class Available extends DefaultSet
{
    protected $attributeClass = 'CollectionAttributeKey';

    /**
     * @param \Concrete\Core\Page\Page $c
     *
     * @return string|null
     */
    public static function getVersionAuthorName($c)
    {
        $vObj = $c->getVersionObject();
        if ($vObj) {
            // h() is required: the search results template renders column values unescaped via JS.
            return h($vObj->getVersionAuthorUserName());
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
        $this->addColumn(new SitemapDisplayOrderColumn());
        $this->addColumn(new PageIDColumn());
        $this->addColumn(new PageTemplateColumn());
        $this->addColumn(new UrlPathColumn());
        parent::__construct();
        $this->addColumn(new Column('cvStatus', t('Version Status'), ['\Concrete\Core\Page\Search\ColumnSet\Available', 'getCollectionVersionStatus'], false));
        // Note: DefaultSet::getCollectionAuthor() shows the page owner (p.uID), not the version author.
        // "Last Edited By" is intentionally a separate column showing who authored the approved version (cvAuthorUID).
        $this->addColumn(new Column('cvAuthorUID', t('Last Edited By'), ['\Concrete\Core\Page\Search\ColumnSet\Available', 'getVersionAuthorName'], false));
    }
}
