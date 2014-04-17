<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Conversations;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;
use \Concrete\Core\Conversation\Rating\Type as ConversationRatingType;

class Points extends DashboardPageController {

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
		$this->redirect('/dashboard/system/conversations/points', 'success');
	}

}