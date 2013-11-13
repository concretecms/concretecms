<?php defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_SearchResponse extends EditResponse {

	protected $result;

	public function setSearchResult(SearchResult $result) {
		$this->result = $result;
	}

	public function getJSONObject() {
		$o = parent::getBaseJSONObject();
		$o->result = $this->result->getJSONObject();
		return $o;
	}
	

}