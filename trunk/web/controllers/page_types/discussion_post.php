<?

Loader::model('discussion_post');
Loader::model('discussion');
class DiscussionPostPageTypeController extends Controller {
	
	public $helpers = array('form', 'html');

	public function on_start() {
		$this->post = DiscussionPostModel::load($this->getCollectionObject());
		$this->error = Loader::helper('validation/error');
		$html = Loader::helper('html');
		$this->addHeaderItem($html->css('discussion'));
		$this->addHeaderItem($html->javascript('discussion'));
		$this->addHeaderItem($html->javascript('facebox/facebox'));
		$this->addHeaderItem('<style type="text/css">@import "' . BASE_URL . DIR_REL . '/js/facebox/facebox.css";</style>');
		$this->post->populateThreadedReplies();
		$replies = $this->post->getReplies();

		$this->set('replies', $replies);
		$this->set("post",$this->post);	
	}

	/** 
	 * Replies to a given discussion or reply
	 */
	public function reply() {
		if ($this->isPost()) {
			$v = Loader::helper('validation/strings');
			$wordFilter = Loader::helper('validation/banned_words');
			if (!$v->notempty($this->post('subject'))) {
				$this->error->add('Your subject cannot be empty.');
			}elseif( $wordFilter->hasBannedWords($this->post('subject')) ){
				$this->error->add('Your subject contains inappropriate content.');
			}
			if (!$v->notempty($this->post('message'))) {
				$this->error->add('Your message cannot be empty.');
			}elseif( $wordFilter->hasBannedWords($this->post('message')) ){
				$this->error->add('Your message contains inappropriate content.');
			}			
			if (!$this->error->has()) {
				if ($this->post('cDiscussionPostParentID') > 0) {
					$dpm2 = DiscussionPostModel::getByID($this->post('cDiscussionPostParentID'));
					$dpm = $dpm2->addPostReply($this->post('subject'), $this->post('message'));
				} else {
					$dpm = $this->post->addPostReply($this->post('subject'), $this->post('message'));
				}
				
				if (is_object($dpm)) {
					$this->redirect($this->post->getCollectionPath() . '#' . $dpm->getCollectionID());
				}
			}
		}
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


	public function on_before_render() {
		$this->set('error', $this->error);
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