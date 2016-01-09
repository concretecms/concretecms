<?php 

namespace Concrete\Controller\SinglePage\Dashboard\System\Attributes\Topics;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;
use \Concrete\Core\Tree\Type\Topic as TopicTree;
use PermissionKey;

class Add extends DashboardPageController {

    public function view()
    {
        $this->set('pageTitle', t('Add Topic Tree'));
    }

	public function submit() {
		$vs = Loader::helper('validation/strings');
		$sec = Loader::helper('security');
		$name = $sec->sanitizeString($this->post('topicTreeName'));
		if (!$this->token->validate('submit')) { 
			$this->error->add(t($this->token->getErrorMessage()));
		}
		if (!$vs->notempty($name)) {
			$this->error->add(t('You must specify a valid name for your tree.'));
		}
		if (!PermissionKey::getByHandle('add_topic_tree')->validate()) {
			$this->error->add(t('You do not have permission to add this tree.'));
		}
		if (!$this->error->has()) {
			$tree = TopicTree::add($name);
			$this->redirect('/dashboard/system/attributes/topics', 'tree_added', $tree->getTreeID());
		}
	}


}