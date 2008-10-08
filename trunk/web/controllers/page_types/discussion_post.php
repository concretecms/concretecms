<?

Loader::model('discussion_post');
Loader::model('discussion');
class DiscussionPostPageTypeController extends Controller {
	
	public $helpers = array('form', 'html');

	public function on_start() {
		$this->post = DiscussionPostModel::load($this->getCollectionObject());
	}

	/** 
	 * Replies to a given discussion or reply
	 */
	public function reply() {
	
	}
	
	public function view() {
		$html = Loader::helper('html');
		$this->addHeaderItem($html->css('discussion'));

		$this->post->populateThreadedReplies();
		$replies = $this->post->getReplies();

		$this->set('replies', $replies);
		$this->set("post",$this->post);	
	}
	
	/** 
	 * The following methods automatically get run and populate the discussionsummary table so that we 
	 * can have quick access to statistics
	 * TESTED
	 */
	public function on_page_add($c) {
		$dpm = DiscussionPostModel::load($c);
		$d = $dpm->getDiscussion();
		$d->updateLastPost($dpm);		
		$dpm->updateParentCounts(1);
	}
	
	/* TESTED */
	public function on_page_move($c, $op, $np) {
		$dpm = DiscussionPostModel::load($c);
		$numToIncrement = 1 + count($dpm->getCollectionChildrenArray());
		$numToDecrement = 0 - $numToIncrement;
		$dpm->updateParentCounts($numToIncrement);
		
		switch($op->getCollectionTypeHandle()) {
			case DiscussionPostModel::CTHANDLE:
				$dpm = DiscussionPostModel::load($op);
				$dpm->updateParentCounts($numToDecrement, true);
				break;
			case DiscussionModel::CTHANDLE:
				// the parent page is the discussion directly, so we decrement it
				$d = DiscussionModel::load($op);
				$d->decrementTotalTopics();
				$d->decrementTotalPosts($numToDecrement);
				break;
		}
	}
	
	/* TESTED */
	public function on_page_duplicate($c, $np) {
		$dpm = DiscussionPostModel::load($c);
		$numToIncrement = 1 + count($dpm->getCollectionChildrenArray());
		$dpm->updateParentCounts($numToIncrement);
	}

	/* TESTED */
	public function on_page_delete($c) {
		$dpm = DiscussionPostModel::load($c);
		$numToDecrement = 1 + count($dpm->getCollectionChildrenArray());
		$num = 0 - $numToDecrement;
		$dpm = DiscussionPostModel::load($c);
		$dpm->updateParentCounts($num);
		
		return true;
	}
}