<?php
namespace Concrete\Controller\SinglePage\Dashboard\Pages\Types;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Loader;
use \Concrete\Core\Page\Type\Composer\FormLayoutSetControl as PageTypeComposerFormLayoutSetControl;
use \Concrete\Core\Page\Type\Composer\FormLayoutSet as PageTypeComposerFormLayoutSet;
use View;
use PageType;


class Form extends DashboardPageController {

	public function view($ptID = false, $message = false) {
		$this->pagetype = PageType::getByID($ptID);
		if (!$this->pagetype) {
			$this->redirect('/dashboard/pages/types');
		}

        $cmp = new \Permissions($this->pagetype);
        if (!$cmp->canEditPageType()) {
            throw new \Exception(t('You do not have access to edit this page type.'));
        }
		switch($message) {
			case 'layout_set_added':
				$this->set('success', t('Form layout set added.'));
				break;
			case 'layout_set_deleted':
				$this->set('success', t('Form layout set deleted.'));
				break;
			case 'layout_set_updated':
				$this->set('success', t('Form layout set updated.'));
				break;
		}
		$this->set('pagetype', $this->pagetype);
		$this->set('sets', PageTypeComposerFormLayoutSet::getList($this->pagetype));
	}

	public function delete_set($ptComposerFormLayoutSetID = false) {
		$set = PageTypeComposerFormLayoutSet::getByID($ptComposerFormLayoutSetID);
		if (!is_object($set)) {
			$this->redirect('/dashboard/pages/types');
		}
		$this->view($set->getPageTypeID());
		if (!$this->token->validate('delete_set')) { 
			$this->error->add(t($this->token->getErrorMessage()));
		}

		if (!$this->error->has()) {
			$set->delete();
			$this->redirect('/dashboard/pages/types/form', $set->getPageTypeID(), 'layout_set_deleted');
		}
	}

	public function update_set($ptComposerFormLayoutSetID = false) {
		$set = PageTypeComposerFormLayoutSet::getByID($ptComposerFormLayoutSetID);
		if (!is_object($set)) {
			$this->redirect('/dashboard/pages/types');
		}
		$this->view($set->getPageTypeID());
		$sec = Loader::helper('security');
		$name = $sec->sanitizeString($this->post('ptComposerFormLayoutSetName'));
		$description = $sec->sanitizeString($this->post('ptComposerFormLayoutSetDescription'));
		if (!$this->token->validate('update_set')) { 
			$this->error->add(t($this->token->getErrorMessage()));
		}

		if (!$this->error->has()) {
			$set->updateFormLayoutSetName($name);
			$set->updateFormLayoutSetDescription($description);
			$this->redirect('/dashboard/pages/types/form', $set->getPageTypeID(), 'layout_set_updated');
		}
	}

	public function delete_set_control() {
		$control = PageTypeComposerFormLayoutSetControl::getByID($this->post('ptComposerFormLayoutSetControlID'));
		if (is_object($control)) {
            $set = $control->getPageTypeComposerFormLayoutSetObject();
            $pt = $set->getPageTypeObject();
            $this->view($pt->getPageTypeID());
			if ($this->token->validate('delete_set_control', $_POST['token'])) {
				$control->delete();
			}
		}
		exit;
	}

	public function add_set($ptID = false) {
		$this->view($ptID);
		$sec = Loader::helper('security');
		$name = $sec->sanitizeString($this->post('ptComposerFormLayoutSetName'));
		$description = $sec->sanitizeString($this->post('ptComposerFormLayoutSetDescription'));
		if ($this->token->validate('add_set')) {
			$set = $this->pagetype->addPageTypeComposerFormLayoutSet($name,$description);
			$this->redirect('/dashboard/pages/types/form', $this->pagetype->getPageTypeID(), 'layout_set_added');
		}
	}

	public function update_set_control_display_order() {
		$fs = PageTypeComposerFormLayoutSet::getByID($_POST['ptComposerFormLayoutSetID']);
		if (is_object($fs)) {
            $pt = $fs->getPageTypeObject();
            $this->view($pt->getPageTypeID());
			if ($this->token->validate('update_set_control_display_order', $_POST['token'])) {
				$displayOrder = 0;
				foreach($this->post('ptComposerFormLayoutSetControlID') as $ptComposerFormLayoutSetControlID) {
					$control = PageTypeComposerFormLayoutSetControl::getByID($ptComposerFormLayoutSetControlID);
					if (is_object($control)) {
						$control->updateFormLayoutSetControlDisplayOrder($displayOrder);
						$displayOrder++;
					}
				}
			}
		}
		exit;
	}

	public function update_set_display_order() {
		$this->view($this->post('ptID'));
		if ($this->token->validate('update_set_display_order', $_POST['token'])) {
			$displayOrder = 0;
			foreach($this->post('ptComposerFormLayoutSetID') as $ptComposerFormLayoutSetID) {
				$set = PageTypeComposerFormLayoutSet::getByID($ptComposerFormLayoutSetID);
				if (is_object($set)) {
					$set->updateFormLayoutSetDisplayOrder($displayOrder);
					$displayOrder++;
				}
			}
		}
		exit;
	}

}