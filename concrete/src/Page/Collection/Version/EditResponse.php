<?php
namespace Concrete\Core\Page\Collection\Version;

use Core;
use PageEditResponse;
use Page;
use Permissions;
use stdClass;

class EditResponse extends PageEditResponse
{
    protected $versions = array();
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
        $dateHelper = Core::make('helper/date');
        /* @var $dateHelper \Concrete\Core\Localization\Service\Date */

        $o = parent::getBaseJSONObject();

        $c = Page::getByID($this->cID);
        $cp = new Permissions($c);
        $cpCanDeletePageVersions = $cp->cpCanDeletePageVersions();

        $versions = array();
        foreach ($this->versions as $v) {
            $obj = new stdClass();
            $obj->cvID = $v->getVersionID();
            $obj->cvIsApproved = $v->isApproved() && !$v->getPublishDate();
            $obj->cvIsScheduled = $v->getPublishDate() != null;
            $obj->cvPublishDate = $dateHelper->formatDateTime($v->getPublishDate());
            $obj->cvPublishEndDate = $dateHelper->formatDateTime($v->getPublishEndDate());
            $obj->cpCanDeletePageVersions = $cpCanDeletePageVersions;
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
