<?php defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Page_Dashboard_System_Conversations_CommunityPoints extends DashboardPageController {

	public function view() {
		$ratingTypes = array_reverse(ConversationRatingType::getList());
		$this->set('ratingTypes', $ratingTypes);
	}

	public function success() {
		$this->view();
		$this->set('message','Rating type updated.');
	}
	
	public function error() {
		$this->error = Loader::helper('validation/error');
		$this->error->add('Invalid rating type specified.');
		$this->view();
		$this->set('error', $this->error);
	}

	public function save() {
		$db = Loader::db();
		$rtID = $this->post('rtID');
		$rtPoints = $this->post('rtPoints');
		if($rtPoints == '') {
			$rtPoints = 0;
		};
		$db->execute('UPDATE ConversationRatingTypes SET cnvRatingTypeCommunityPoints = ? WHERE cnvRatingTypeID = ?', array($rtPoints, $rtID));
		$this->redirect('/dashboard/system/conversations/community_points/success');
	}

}