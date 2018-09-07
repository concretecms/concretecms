<?php
namespace Concrete\Core\Page\Collection\Version;

use Core;
use PageEditResponse;
use Concrete\Core\Page\Page;
use Concrete\Core\Area\Area;
use Permissions;
use stdClass;

class EditResponse extends PageEditResponse
{
    protected $versions = array();
    protected $vl;

    /**
     * The editing area (only in case of stacks)
     *
     * @var \Concrete\Core\Area\Area|null
     */
    protected $stackArea = null;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Page\EditResponse::setPage()
     */
    public function setPage(Page $page)
    {
        parent::setPage($page);
        if ($page->getPageTypeHandle() === STACKS_PAGE_TYPE) {
            $this->stackArea = Area::get($page, STACKS_AREA_NAME);
        } else {
            $this->stackArea = null;
        }
    }

    public function addCollectionVersion(Version $cv)
    {
        $this->versions[] = $cv;
    }

    public function getCollectionVersions()
    {
        return $this->versions;
    }
    public function setVersionList(VersionList $vl)
    {
        $this->vl = $vl;
    }

    public function getJSONObject()
    {
        $dateHelper = Core::make('helper/date');
        /* @var $dateHelper \Concrete\Core\Localization\Service\Date */

        $o = parent::getBaseJSONObject();

        $c = Page::getByID($this->cID);
        if ($this->stackArea === null) {
            $cp = new Permissions($c);
            $cpCanDeletePageOrAreaVersions = $cp->canDeletePageVersions();
        } else {
            $cp = new Permissions($this->stackArea);
            $cpCanDeletePageOrAreaVersions = $cp->canDeleteAreaVersions();
        }
        $versions = array();
        foreach ($this->versions as $v) {
            $obj = new stdClass();
            $obj->cvID = $v->getVersionID();
            $obj->cvIsApproved = $v->isApproved() && !$v->getPublishDate();
            $obj->cvIsScheduled = $v->getPublishDate() != null;
            $obj->cvPublishDate = $dateHelper->formatDateTime($v->getPublishDate());
            $obj->cvPublishEndDate = $dateHelper->formatDateTime($v->getPublishEndDate());
            $obj->cpCanDeletePageOrAreaVersions = $cpCanDeletePageOrAreaVersions;
            $obj->cvDateVersionCreated = $dateHelper->formatDateTime($v->getVersionDateCreated());
            $obj->cvApprovedDate = $dateHelper->formatDateTime($v->getVersionDateApproved());
            $obj->cvAuthorUserName = $v->getVersionAuthorUserName();
            $obj->cvApproverUserName = $v->getVersionApproverUserName();
            $obj->cvComments = $v->getVersionComments();
            $obj->cIsStack = ($c->getCollectionTypeHandle() === STACKS_PAGE_TYPE);
            $versions[] = $obj;
        }
        $o->versions = $versions;
        if (is_object($this->vl)) {
            $ph = $this->vl->getPagination();
            $o->hasNextPage = $ph->hasNextPage();
            $o->hasPreviousPage = $ph->hasPreviousPage();
            $o->currentPage = $ph->getCurrentPage();
            $o->nextPageNum = $ph->getNextInt() + 1;
            $o->previousPageNum = $ph->getPreviousInt() + 1;
        }

        return $o;
    }
}
