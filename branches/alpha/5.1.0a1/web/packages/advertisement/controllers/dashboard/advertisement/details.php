<?

defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('advertisement_details', 'advertisement');

class DashboardAdvertisementDetailsController extends Controller {

	public function on_start() {
		$subnav = array(
			array(View::url('/dashboard/advertisement'), 'Advertisements',false),
			array(View::url('/dashboard/advertisement/groups'), 'Groups', false)
		);
		$this->set('subnav', $subnav);
	}
	
	
	function load_details($aID = NULL) {
		$ad = new AdvertisementDetails();
		if($aID) {
			$ad->load("aID=".$aID);
		} else {
			$ad->targetImpressions = 1000;
			$ad->targetClickThrus = 1000;
		}
		
		$al = Loader::helper('concrete/asset_library');

		if ($ad->fID > 0) { 
			$this->set("bf",$ad->getFileObject());
		}	
		$this->set("ad",$ad);
	}
	
	function save_details($aID = NULL) {
		$error = array();
		$ad = new AdvertisementDetails();
		if($aID) {
			$ad->load("aID=".$aID);
		}
		
		// validate post array
		if(!strlen($_POST['name'])) {
			$error[] = "Enter a name";
		}
		
		if(!strlen($_POST['html'])) {
			if(!$_POST['fID']) {
				$error[] = "Select an image";
			}
			if(!$_POST['url']) {
				$error[] = "enter a url";
			}
		}
		if(count($error) == 0) {
			$ad->save($_POST);
			$this->load_details($ad->aID);
			$this->set("message",($aID?"Advertisement Updated":"Advertisement Created"));
		} else {
			$this->set("error",$error);
			$ad->url = $_POST['url'];
			$ad->name = $_POST['name'];
			$ad->fID = $_POST['fID'];
			$ad->agIDs = $_POST['adGroupIDs'];
			$ad->targetImpressions = $_POST['targetImpressions'];
			$ad->targetClickThrus = $_POST['targetClickThrus'];
			$ad->html = $_POST['html'];
			$this->set("ad",$ad);
		}		
	}
}
?>