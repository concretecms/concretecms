<?
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardComposerController extends Controller {

	public function view() {
		// grab all composerable page types
		$ctArray = CollectionType::getComposerPageTypes();
		if (count($ctArray) == 1) {
			$ct = $ctArray[0];
			$this->redirect('/dashboard/composer/write', $ct->getCollectionTypeID());
			exit;
		}
		$this->set('ctArray', $ctArray);
	}
	
}