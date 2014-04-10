<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Page_Dashboard_Pages_Types extends DashboardPageController {

	public function page_type_added() {
		$this->set('success', t('Page Type added successfully.'));
		$this->view();
	}

	public function page_type_updated() {
		$this->set('success', t('Page type updated successfully.'));
		$this->view();
	}

	public function page_type_deleted() {
		$this->set('success', t('Page type deleted successfully.'));
		$this->view();
	}

	public function edit($ptID = false) {
		$cm = PageType::getByID($ptID);
		$this->set('pagetype', $cm);
	}

	public function view() {
		$pagetypes = PageType::getList();
		$this->set('pagetypes', $pagetypes);
	}

	public function delete($ptID = false) {
		$pagetype = PageType::getByID($ptID);
		if (!is_object($pagetype)) {
			$this->error->add(t('Invalid page type object.'));
		}
		if (!$this->token->validate('delete_page_type')) { 
			$this->error->add(t($this->token->getErrorMessage()));
		}
		if (!$this->error->has()) {
			$pagetype->delete();
			$this->redirect('/dashboard/pages/types', 'page_type_deleted');
		}
	}
	
	public function submit($ptID = false) {
		$pagetype = PageType::getByID($ptID);
		if (!is_object($pagetype)) {
			$this->error->add(t('Invalid page type object.'));
		}
		if (!$this->token->validate('update_page_type')) { 
			$this->error->add(t($this->token->getErrorMessage()));
		}

		$vs = Loader::helper('validation/strings');
		$sec = Loader::helper('security');
		$name = $sec->sanitizeString($this->post('ptName'));
		$handle = $sec->sanitizeString($this->post('ptHandle'));
		if (!$vs->notempty($name)) {
			$this->error->add(t('You must specify a valid name for your page type.'));
		}
		if (!$vs->handle($handle)) {
			$this->error->add(t('You must specify a valid handle for your page type.'));
		}
		$defaultTemplate = PageTemplate::getByID($this->post('ptDefaultPageTemplateID'));
		if (!is_object($defaultTemplate)) {
			$this->error->add(t('You must choose a valid default page template.'));
		}
		$templates = array();
		if (is_array($_POST['ptPageTemplateID'])) {
			foreach($this->post('ptPageTemplateID') as $pageTemplateID) {
				$pt = PageTemplate::getByID($pageTemplateID);
				if (is_object($pt)) {
					$templates[] = $pt;
				}
			}
		}

		if (count($templates) == 0 && $this->post('ptAllowedPageTemplates') == 'C') {
			$this->error->add(t('You must specify at least one page template.'));
		}
		$target = PageTypePublishTargetType::getByID($this->post('ptPublishTargetTypeID'));
		if (!is_object($target)) {
			$this->error->add(t('Invalid page type target type.'));
		}
		if (!$this->error->has()) {
			$data = array(
				'handle' => $handle,
				'name' => $name,
				'defaultTemplate' => $defaultTemplate,
				'ptLaunchInComposer' => $this->post('ptLaunchInComposer'),
				'allowedTemplates' => $this->post('ptAllowedPageTemplates'),
				'templates' => $templates
			);
			$pagetype->update($data);
			$configuredTarget = $target->configurePageTypePublishTarget($pagetype, $this->post());
			$pagetype->setConfiguredPageTypePublishTargetObject($configuredTarget);
			$this->redirect('/dashboard/pages/types', 'page_type_updated');
		}
	}
}