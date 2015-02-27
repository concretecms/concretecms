<?php
namespace Concrete\Controller\SinglePage\Dashboard\Blocks;
use Concrete\Core\Page\Collection\Version\Version;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use StackList, Stack;
use Page;
use Permissions;
use Loader;
use User;
use \Concrete\Core\Workflow\Request\DeletePageRequest;
use \Concrete\Core\Workflow\Request\ApproveStackRequest;
use View;
use Exception;
use Redirect;

class Stacks extends DashboardPageController {

    public function view_global_areas()
    {
        $stm = new StackList();
        $stm->filterByGlobalAreas();
        $this->set('stacks', $stm->get());
    }

	public function view() {
        $stm = new StackList();
        $stm->filterByUserAdded();
        $this->set('stacks', $stm->get());

		$parent = Page::getByPath(STACKS_PAGE_PATH);
		$cpc = new Permissions($parent);
		if ($cpc->canMoveOrCopyPage()) {
			$this->set('canMoveStacks', true);
            $sortUrl = View::url('/dashboard/blocks/stacks', 'update_order');
            $this->set('sortURL', $sortUrl);
		}
	}

	public function add_stack() {
		if (Loader::helper('validation/token')->validate('add_stack')) {
			if (Loader::helper('validation/strings')->notempty($this->post('stackName')))  {
				$stack = Stack::addStack($this->post('stackName'));
				$this->redirect('/dashboard/blocks/stacks', 'view_details', $stack->getCollectionID(), 'stack_added');
			} else {
				$this->error->add(t("You must give your stack a name."));
			}
		} else {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
		}
	}

	public function stack_deleted() {
		$this->set('message', t('Stack deleted successfully'));
		$this->view();
	}

    public function delete_stack() {
		if (Loader::helper('validation/token')->validate('delete_stack')) {
			$s = Stack::getByID($_REQUEST['stackID']);
			if (is_object($s)) {
				$sps = new Permissions($s);
				if ($sps->canDeletePage()) {
					$u = new User();
					$pkr = new DeletePageRequest();
					$pkr->setRequestedPage($s);
					$pkr->setRequesterUserID($u->getUserID());
					$response = $pkr->trigger();
					if ($response instanceof \Concrete\Core\Workflow\Progress\Response) {
						// we only get this response if we have skipped workflows and jumped straight in to an approve() step.
						$this->redirect('/dashboard/blocks/stacks', 'stack_deleted');
					} else {
						$this->redirect('/dashboard/blocks/stacks', 'view_details', $s->cID, 'delete_saved');
					}
				} else {
					$this->error->add(t('You do not have access to delete this stack.'));
				}
			} else {
				$this->error->add(t('Invalid stack'));
			}
		} else {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
		}
	}


    public function approve_stack($stackID = false, $token = false) {
        if (Loader::helper('validation/token')->validate('approve_stack', $token)) {
            $s = Stack::getByID($stackID);
            if (is_object($s)) {
                $sps = new Permissions($s);
                if ($sps->canApprovePageVersions()) {
                    $u = new User();
                    $v = Version::get($s, 'RECENT');
                    $pkr = new ApproveStackRequest();
                    $pkr->setRequestedPage($s);
                    $pkr->setRequestedVersionID($v->getVersionID());
                    $pkr->setRequesterUserID($u->getUserID());
                    $response = $pkr->trigger();
                    if ($response instanceof \Concrete\Core\Workflow\Progress\Response) {
                        // we only get this response if we have skipped workflows and jumped straight in to an approve() step.
                        $this->redirect('/dashboard/blocks/stacks', 'view_details', $stackID, 'stack_approved');
                    } else {
                        $this->redirect('/dashboard/blocks/stacks', 'view_details', $stackID, 'approve_saved');
                    }
                } else {
                    $this->error->add(t('You do not have access to approve this stack.'));
                }
            } else {
                $this->error->add(t('Invalid stack'));
            }
        } else {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
    }

	public function view_details($cID, $msg = false) {
		$s = Stack::getByID($cID);
		if (is_object($s)) {
			$blocks = $s->getBlocks('Main');
			$view = View::getInstance();
			foreach($blocks as $b1) {
				$btc = $b1->getInstance();
				// now we inject any custom template CSS and JavaScript into the header
                if ($btc instanceof \Concrete\Core\Block\BlockController) {
					$btc->outputAutoHeaderItems();
				}
				$btc->runTask('on_page_view', array($view));
			}
			$this->addHeaderItem($s->outputCustomStyleHeaderItems(true));

			$this->set('stack', $s);
			$this->set('blocks', $blocks);
			switch($msg) {
                case 'stack_added':
                    $this->set('message', t('Stack added successfully.'));
				    break;
                case 'stack_approved':
                    $this->set('message', t('Stack approved successfully'));
                    break;
                case 'approve_saved':
                    $this->set('message', t('Approve request saved. You must complete the approval workflow before these changes are publicly accessible.'));
                    break;
                case 'delete_saved':
					$this->set('message', t('Delete request saved. You must complete the delete workflow before this stack can be deleted.'));
					break;
				case 'rename_saved':
					$this->set('message', t('Rename request saved. You must complete the approval workflow before the name of the stack will be updated.'));
					break;
			}
		} else {
			throw new Exception(t('Invalid stack'));
		}
	}

	public function rename($cID) {
		$s = Stack::getByID($cID);
		if (is_object($s)) {
			$this->set('stack', $s);
		} else {
			throw new Exception(t('Invalid stack'));
		}
		$sps = new Permissions($s);
		if (!$sps->canEditPageProperties()) {
			$this->redirect('/dashboard/blocks/stacks', 'view_details', $cID);
		}

		if ($this->isPost()) {
			if (Loader::helper('validation/token')->validate('rename_stack')) {
				if (Loader::helper('validation/strings')->notempty($stackName = trim($this->post('stackName')))) {
					$txt = Loader::helper('text');
					$v = $s->getVersionToModify();
					$v->update(array(
						'cName' => $stackName,
						'cHandle' => str_replace('-', Config::get('concrete.seo.page_path_separator'), $txt->urlify($stackName))
					));

					$u = new User();
					$pkr = new ApproveStackRequest();
					$pkr->setRequestedPage($s);
					$pkr->setRequestedVersionID($v->getVersionID());
					$pkr->setRequesterUserID($u->getUserID());
					$response = $pkr->trigger();
                    if ($response instanceof \Concrete\Core\Workflow\Progress\Response) {
						// we only get this response if we have skipped workflows and jumped straight in to an approve() step.
						$this->redirect('/dashboard/blocks/stacks', 'stack_renamed', $cID);
					} else {
						$this->redirect('/dashboard/blocks/stacks', 'view_details', $cID, 'rename_saved');
					}
				} else {
					$this->error->add(t("The stack name cannot be empty."));
				}
			} else {
				$this->error->add(Loader::helper('validation/token')->getErrorMessage());
			}
		}
	}

	public function stack_renamed($cID) {
		$this->set('message', t('Stack renamed successfully'));
		$this->view_details($cID);
		$this->task = 'view_details';
	}

	public function duplicate($cID) {
		$s = Stack::getByID($cID);
		if (is_object($s)) {
			$this->set('stack', $s);
		} else {
			throw new Exception(t('Invalid stack'));
		}
		$sps = new Permissions($s);
		if (!$sps->canMoveOrCopyPage()) {
			$this->redirect('/dashboard/blocks/stacks', 'view_details', $cID);
		}

		if ($this->isPost()) {
			if (Loader::helper('validation/token')->validate('duplicate_stack')) {
				if (Loader::helper('validation/strings')->notempty($stackName = trim($this->post('stackName'))))  {
					$ns = $s->duplicate();
					$ns->update(array(
						'stackName' => $stackName
					));

					$this->redirect('/dashboard/blocks/stacks', 'stack_duplicated');
				} else {
					$this->error->add(t("You must give your stack a name."));
				}
			} else {
				$this->error->add(Loader::helper('validation/token')->getErrorMessage());
			}
			$name = trim($this->post('name'));
		}
	}

	public function stack_duplicated() {
		$this->set('message', t('Stack duplicated successfully'));
		$this->view();
	}

	public function update_order() {
		$ret = array('success' => false, 'message' => t("Error"));
		if ($this->isPost() && is_array($stIDs = $this->post('stID'))) {
			$parent = Page::getByPath(STACKS_PAGE_PATH);
			$cpc = new Permissions($parent);
			if ($cpc->canMoveOrCopyPage()) {
				foreach($stIDs as $displayOrder => $cID) {
					$c = Page::getByID($cID);
					$c->updateDisplayOrder($displayOrder, $cID);
				}
				$ret['success'] = true;
				$ret['message'] = t("Stack order updated successfully.");
			}
		}
		echo Loader::helper('json')->encode($ret);
		exit;
	}

    public function list_page() {
        return Redirect::to('/');
    }

}
