<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Library_FileSearchResultItem extends SearchResultItem {

	public $fID;

	public function __construct(SearchResult $result, DatabaseItemListColumnSet $columns, $item) {
		parent::__construct($result, $columns, $item);
		$this->populateDetails($item);
	}

	protected function populateDetails($item) {
		$this->fID = $item->getFileID();
		$this->isStarred = $item->isStarred();
		$fp = new Permissions($item);
		$this->canCopyFile = $fp->canCopyFile();
		$this->canEditFilePermissions = $fp->canEditFilePermissions();
		$this->canDeleteFile = $fp->canDeleteFile();
		$this->canViewFile = $item->canView();
		$this->canEditFile = $item->canEdit();
		$this->canReplaceFile = $fp->canEditFileContents();
	}


}
