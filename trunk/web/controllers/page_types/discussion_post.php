<?

Loader::model('discussion_post');
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
}