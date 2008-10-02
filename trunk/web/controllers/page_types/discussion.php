<?

Loader::model('discussion');
class DiscussionPageTypeController extends Controller {

	/** 
	 * Returns all the posts beneath a given discussion
	 */
	public function view() {
		$c = $this->getCollectionObject();
		$dm = DiscussionModel::load($c);
		$posts = $dm->getPosts();
	}
	
	/** 
	 * Creates a top level post in a discussion
	 */
	public function post() {
		if ($this->isPost()) {
		
		} else {
			// we are viewing the page
		}
	}
	
}