<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Conversations_Messages extends DashboardBaseController {

	public function view() {
		$ml = new ConversationMessageList();
		$ml->setItemsPerPage(20);
		$cmpFilterTypes = array(
			'approved' => t('Approved'),
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

		if ($_REQUEST['cmpMessageKeywords']) {
			$ml->filterByKeywords($_REQUEST['cmpMessageKeywords']);
		}
		if ($_REQUEST['cmpMessageFilter'] && $_REQUEST['cmpMessageFilter'] != 'approved') {
			switch($_REQUEST['cmpMessageFilter']) {
				case 'deleted':
					$ml->filterByDeleted();
					break;
				default: // flag
					$flagtype = ConversationFlagType::getByHandle($_REQUEST['cmpMessageFilter']);
					if (is_object($flagtype)){
						$ml->filterByFlag($flagtype);
					} else {
						$ml->filterByApproved();
					}
					break;

			}
		} else {
			$ml->filterByApproved();
		}
		if ($_REQUEST['cmpMessageSort'] == 'date_asc') {
			$ml->sortByDateAscending();
		} else {
			$ml->sortByDateDescending();
		}
		
		$this->set('list', $ml);
		$this->set('messages', $ml->getPage());
		$this->set('cmpFilterTypes', $cmpFilterTypes);
		$this->set('cmpSortTypes', $cmpSortTypes);
	}

}