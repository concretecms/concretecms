<?php 
namespace Concrete\Core\Search;
use \Concrete\Core\Application\EditResponse;
class Response extends EditResponse {

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