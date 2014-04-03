<?php
namespace Concrete\Core\Page;
use Loader;
class EditVersionResponse extends EditResponse {

	protected $versions = array();
	protected $vl;

	public function addCollectionVersion(CollectionVersion $cv) {
		$this->versions[] = $cv;
	}
	
	public function getCollectionVersions() {
		return $this->versions;
	}
	public function setVersionList(VersionList $vl) {
		$this->vl = $vl;
	}

	public function getJSONObject() {
		$o = parent::getBaseJSONObject();

		$c = Page::getByID($this->cID);
		$cp = new Permissions($c);
		$cpCanDeletePageVersions = $cp->cpCanDeletePageVersions();
		
		$versions = array();
		foreach($this->versions as $v) {
			$obj = new stdClass;
			$obj->cvID = $v->getVersionID();
			$obj->cvIsApproved = $v->isApproved();
			$obj->cpCanDeletePageVersions = $cpCanDeletePageVersions;
			$obj->cvDateVersionCreated = date(DATE_APP_PAGE_VERSIONS, strtotime($v->getVersionDateCreated('user')));
			$obj->cvAuthorUserName = $v->getVersionAuthorUserName();
			$obj->cvComments = $v->getVersionComments();
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