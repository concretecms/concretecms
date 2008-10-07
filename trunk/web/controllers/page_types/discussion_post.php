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
		$this->set("post",$this->post);	
	}
	
	/** 
	 * The following methods automatically get run and populate the discussionsummary table so that we 
	 * can have quick access to statistics
	 */
	public function on_page_add($c) {
		DiscussionPostPageTypeController::incrementPostsAndTopics($c);
		$dpm = DiscussionPostModel::load($c);
		$d = $dpm->getDiscussion();
		$d->updateLastPost($dpm);
	}
	
	public function on_page_move($c, $op, $np) {
		$numChildren = DiscussionPostPageTypeController::incrementPostsAndTopics($c);
		switch($op->getCollectionTypeHandle()) {
			case DiscussionPostModel::CTHANDLE:
				$dpm = DiscussionPostModel::load($op);
				$d = $dpm->getDiscussion();
				$d->decrementTotalPosts($numChildren);
				break;
			case DiscussionModel::CTHANDLE:
				// the parent page is the discussion directly, so we decrement it
				$d = DiscussionModel::load($op);
				$d->decrementTotalTopics();
				$d->decrementTotalPosts($numChildren);
				break;
		}
	}
	
	private function incrementPostsAndTopics($c) {
		$dpm = DiscussionPostModel::load($c);
		$d = $dpm->getDiscussion();
		$numChildren = 1 + count($dpm->getCollectionChildrenArray());
		$d->incrementTotalPosts($numChildren);
		
		// now we check to see if the immediate parent of the newly added page is a discussion
		// if that's the case, then we ALSO increment total topics (because a top level discussion post is a topic)
		if ($dpm->getCollectionParentID() == $d->getCollectionID()) {
			$d->incrementTotalTopics();
		}
		
		// returns the total number of posts affected
		return $numChildren;
	}
	public function on_page_duplicate($c, $np) {
		DiscussionPostPageTypeController::incrementPostsAndTopics($c);
	}
	
	public function on_page_delete($c) {
		$dpm = DiscussionPostModel::load($c);
		$d = $dpm->getDiscussion();
		$numChildren = 1 + count($dpm->getCollectionChildrenArray());
		$d->decrementTotalPosts($numChildren);
		if ($dpm->getCollectionParentID() == $d->getCollectionID()) {
			$d->decrementTotalTopics();
		}
		
		return true;
	}
}