<?
Loader::model("advertisement/advertisement_group");

class DashboardAdvertisementGroupsController extends Controller {

	public function on_start() {
		$subnav = array(
			array(View::url('/dashboard/advertisement'), 'Advertisements',false),
			array(View::url('/dashboard/advertisement/groups'), 'Groups', true)
		);
		$this->set('subnav', $subnav);
	}

	public function save_group($agID = NULL) {
	
		$ag = new AdvertisementGroup();
		if($agID) {
			$ag->load("agID=".$agID);
			$task = "edit";
		} else {
			$task = "add";
		}
		
		if (!$_POST['agName']) {
			$error[] = "Name required.";
		}
		if (count($error) == 0) {
			$ag->agName = $_POST['agName'];
			$ag->save();
			if ($task=="add") {
				$this->set("message","Group Created");
			} else {
				$this->set("message","Group Updated");
			}				
		} else {
			$this->set("error",$error);
		}
	}
	
	public function load_group($agID) {
		$ag_edit = new AdvertisementGroup();
		if($agID) {
			$ag_edit->load("agID=".$agID);
			$this->set("ag_edit",$ag_edit);
		}
	}
	
	public function delete_group($agID) {
		$ag = new AdvertisementGroup();
		$ag->load("agID=".$agID);
		if (is_object($ag)) {
			$ag->delete();
			$this->set("message","Group Deleted");
		}
	}

}
?>