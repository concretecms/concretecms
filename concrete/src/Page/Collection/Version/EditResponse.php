<?php

namespace Concrete\Core\Page\Collection\Version;

use Concrete\Core\Page\EditResponse as PageEditResponse;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker as Permissions;
use Concrete\Core\Support\Facade\Application;
use stdClass;

class EditResponse extends PageEditResponse
{
    /**
     * @var \Concrete\Core\Page\Collection\Version\Version[]
     */
    protected $versions = [];

    protected $vl;

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
        $dateHelper = Application::getFacadeApplication()->make('helper/date');
        // @var $dateHelper \Concrete\Core\Localization\Service\Date

        $o = parent::getBaseJSONObject();

        $c = Page::getByID($this->cID);
        $cp = new Permissions($c);
        $cpCanDeletePageVersions = $cp->cpCanDeletePageVersions();

        $versions = [];
        foreach ($this->versions as $v) {
            $obj = new stdClass();
            $obj->cvID = $v->getVersionID();
            $obj->cvIsApproved = (bool) $v->isApproved();
            $obj->cvIsApprovedNow = $v->isApprovedNow();
            $obj->cvIsScheduled = $v->getPublishDate() || $v->getPublishEndDate();
            $obj->cvPublishDate = $dateHelper->formatDateTime($v->getPublishDate());
            $obj->cvPublishEndDate = $dateHelper->formatDateTime($v->getPublishEndDate());
            $obj->cpCanDeletePageVersions = $cpCanDeletePageVersions;
            $obj->cvDateVersionCreated = $dateHelper->formatDateTime($v->getVersionDateCreated());
            $obj->cvApprovedDate = $dateHelper->formatDateTime($v->getVersionDateApproved());
            $obj->cvAuthorUserName = $v->getVersionAuthorUserName();
            $obj->cvApproverUserName = $v->getVersionApproverUserName();
            $obj->cvComments = $v->getVersionComments();
            $obj->cIsStack = ($c->getPageTypeHandle() === STACKS_PAGE_TYPE);
            $obj->cIsDraft = $c->isPageDraft();
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
