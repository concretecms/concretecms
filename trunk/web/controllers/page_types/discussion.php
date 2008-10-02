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
	
}