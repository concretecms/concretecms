<?
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('advertisement_details', 'advertisement');

class DashboardAdvertisementController extends Controller {

	public function on_start() {
		$subnav = array(
			array(View::url('/dashboard/advertisement'), 'Advertisements',true),
			array(View::url('/dashboard/advertisement/groups'), 'Groups', $prefsSelected)
		);
		$this->set('subnav', $subnav);
	}

	public function delete_advertisement($aID) {
		$ad = new AdvertisementDetails();
		$ad->load("aID=".$aID);
		$ad->delete();
		$this->set("message","Advertisement Deleted");
	}
}
?>