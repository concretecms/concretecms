<?php defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_System_Conversations_CommunityPoints extends Controller {

	public function view() {
		$ratingTypes = array_reverse(ConversationRatingType::getList());
		$this->set('ratingTypes', $ratingTypes);
	}
	
	public function manage($typeID) {
		$ratingType = ConversationRatingType::getByID($typeID);
		$this->set('ratingType', $ratingType);
	}

	public function success() {
		$this->view();
		$this->set('message','Updated rating type.');
	}

	public function error() {
		$this->error = Loader::helper('validation/error');
		$this->error->add('Invalid rating type specified.');
		$this->view();
		$this->set('error',$this->error);
	}

	public function save() {
		/*
		$this->view();
		$active = $this->post('activeEditor');
		
		$db = Loader::db();

		if (!isset($this->editors[$active])) {
			$this->redirect('/dashboard/system/conversations/editor/error');
			return;
		}*/
		$rtID = $this->post('rtID');
		$rtName = $this->post('rtName');
		$rtPoints = $this->post('rtPoints');
		$rtHandle = $this->post('rtHandle');
		$db = Loader::db();
		$db->execute('UPDATE ConversationRatingTypes SET cnvRatingTypeName = ? WHERE cnvRatingTypeID = ?', array($rtName, $rtID));
		$db->execute('UPDATE ConversationRatingTypes SET cnvRatingTypeCommunityPoints = ? WHERE cnvRatingTypeID = ?', array($rtPoints, $rtID));
		$db->execute('UPDATE ConversationRatingTypes SET cnvRatingTypeHandle = ? WHERE cnvRatingTypeID = ?', array($rtHandle, $rtID));
		$this->redirect('/dashboard/system/conversations/community_points/success');
	}

}