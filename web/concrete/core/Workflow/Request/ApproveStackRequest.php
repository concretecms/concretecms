<?
namespace Concrete\Core\Page\Workflow\Request;
use Loader;
use \Concrete\Core\Page\Collection\Version as CollectionVersion;
use Stack;
use EVents;
use \Concrete\Workflow\Workflow\Progress\Response as WorkflowProgressResponse;
use \Concrete\Workflow\Workflow\Progress\Response as WorkflowProgressResponse;

class ApproveStackRequest extends Request {

	public function approve(WorkflowProgress $wp) {
		$s = Stack::getByID($this->getRequestedPageID());
		$v = CollectionVersion::get($s, $this->cvID);
		$v->approve(false);
		if ($s->getStackName() != $v->getVersionName()) {
			// The stack name has changed so we need to
			// update that for the stack object as well.
			$s->update(array('stackName' => $v->getVersionName()));
		}

		$ev = \Concrete\Core\Page\Collection\Version\Event($s);
		$ev->setCollectionVersionObject($v);
		Events::dispatch('on_page_version_submit_approve', $ev);

		$wpr = new WorkflowProgressResponse();
		$wpr->setWorkflowProgressResponseURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $s->getCollectionID());
		return $wpr;
	}

	
}