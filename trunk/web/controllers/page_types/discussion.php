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
		$this->set('posts', $this->discussion->getPosts());
	}
	
	/** 
	 * Creates a top level post in a discussion
	 */
	public function add() {
		if ($this->isPost()) {
			$v = Loader::helper('validation/strings');
			if (!$v->notempty($this->post('subject'))) {
				$this->error->add('Your subject cannot be empty.');
			}			
			if (!$v->notempty($this->post('message'))) {
				$this->error->add('Your message cannot be empty.');
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
	
}