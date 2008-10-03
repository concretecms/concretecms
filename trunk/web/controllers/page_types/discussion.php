<?

Loader::model('discussion');
class DiscussionPageTypeController extends Controller {

	public $helpers = array('form', 'html'); 
	
	/** 
	 * Returns all the posts beneath a given discussion
	 */
	public function on_start() {
		$c = $this->getCollectionObject();
		$dm = DiscussionModel::load($c);
		$posts = $dm->getPosts();
		$this->set('posts', $posts);
	}
	
	/** 
	 * Creates a top level post in a discussion
	 */
	public function add() {
		if ($this->isPost()) {
			print 'add to db';
		
		} else {
			// we are viewing the page
		}
	}
	
}