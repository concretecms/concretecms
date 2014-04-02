<?
namespace Concrete\Core\Page\Search\Result;
use \Concrete\Core\Search\Result\Item as SearchItem;
class Item extends SearchItem {

	public $cID;

	public function __construct(SearchResult $result, DatabaseItemListColumnSet $columns, $item) {
		$list = $result->getItemListObject();
		if ($list->isIndexedSearch()) {
			$this->columns[] = new SearchResultItemColumn(t('Score'), $item->getPageIndexScore());
		}
		parent::__construct($result, $columns, $item);
		$this->populateDetails($item);
	}

	protected function populateDetails($item) {
		$this->cID = $item->getCollectionID();
		$cp = new Permissions($item);        
		$this->canEditPageProperties = $cp->canEditPageProperties();
		$this->canEditPageSpeedSettings = $cp->anEditPageSpeedSettings();
		$this->canEditPagePermissions = $cp->canEditPagePermissions();
		$this->canEditPageDesign = $cp->canEditPageDesign();
		$this->canViewPageVersions = $cp->canViewPageVersions();
		$this->canDeletePage = $cp->canDeletePage();
		$this->cvName = $item->getCollectionName();
	}


}
