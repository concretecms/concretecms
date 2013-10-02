<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_IndexedSearchResult {

	public function __construct($id, $name, $description, $score, $cPath, $content, $cDatePublic = false) {
		$this->cID = $id;
		$this->cName = $name;
		$this->cDescription = $description;		
		$this->score = $score;
		$this->cPath = $cPath;
		$this->content = $content;
		if ($cDatePublic) {
			$this->setDate($cDatePublic); 
		}
		$this->nh = Loader::helper('navigation');
	}

	public function getID() {return $this->cID;}
	public function getName() {return $this->cName;}
	public function getDescription() {return $this->cDescription;}
	public function getScore() {return $this->score;}
	public function getCollectionPath() {return $this->cPath;}
	public function getCpath() {return $this->cPath;}
	public function getBodyContent() {return $this->content;}
	public function getDate($mask = '') {
		if ($mask == '') {
			$mask = DATE_APP_DASHBOARD_SEARCH_RESULTS_PAGES;
		}
		return date($mask, strtotime($this->cDate));
	}
	public function getPath() {
		$c = Page::getByID($this->cID);
		return $this->nh->getLinkToCollection($c, true);
	}
	
	public function setDate($date) { $this->cDate = $date;}
}
