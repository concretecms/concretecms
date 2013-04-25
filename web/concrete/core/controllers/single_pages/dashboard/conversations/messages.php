<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Conversations_Messages extends DashboardBaseController {

	public function view() {
		$ml = new ConversationMessageList();
		$this->set('messages', $ml->getPage());
		$cmpFilterTypes = array(
			'approved' => t('Approved'),
			'pending' => t('Pending'),
			'deleted' => t('Deleted')
		);
		$fl = new ConversationFlagTypeList();
		foreach($fl->get() as $flagtype) {
			$cmpFilterTypes[$flagtype->getConversationFlagTypeHandle()] = Loader::helper('text')->unhandle($flagtype->getConversationFlagTypeHandle());
		}
		$cmpSortTypes = array(
			'date_desc' => t('Recent First'),
			'date_asc' => t('Earliest First')
		);

		$this->set('cmpFilterTypes', $cmpFilterTypes);
		$this->set('cmpSortTypes', $cmpSortTypes);
	}

}