<?
Loader::model('discussion');
class DiscussionPageTypeController extends Controller {

	public $helpers = array('form', 'html'); 
	private $error = false;
	private $discussion;

	/** 
	 * Returns all the posts beneath a given discussion
	 */
	public function on_start() {
		$this->error = Loader::helper('validation/error');
		$this->discussion = DiscussionModel::load($this->getCollectionObject());
		//$this->set('posts', $this->discussion->getPosts());
	}
	
	/** 
	 * Creates a top level post in a discussion
	 */
	public function add() {
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
				$dpm = $this->discussion->addPost($this->post('subject'), $this->post('message'));
				$nh = Loader::helper('navigation');
				$this->redirect($dpm->getCollectionPath());
			}
		} else {

		}
		$this->render('/add_discussion_post');
	}
	
	public function on_before_render() {
		$this->set('error', $this->error);
	}
	
	public static function on_page_view($c) {
		$db = Loader::db();
		$dm = DiscussionModel::load($c);
		$dm->recordView();
	}
}