<?php
namespace Concrete\Controller\SinglePage\Dashboard\Pages\Types;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Attribute\Key\CollectionKey;
use \Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use \Concrete\Core\Page\Controller\DashboardPageController;
use PageType;
use Loader;

class Attributes extends DashboardPageController
{

    protected $pagetype;
    protected $defaultPage;

    protected function setupPageType($ptID)
    {
        $this->pagetype = PageType::getByID($ptID);
        if (!$this->pagetype) {
            $this->redirect('/dashboard/pages/types');
        }
        $cmp = new \Permissions($this->pagetype);
        if (!$cmp->canEditPageType()) {
            throw new \Exception(t('You do not have access to edit this page type.'));
        }
        $this->defaultPage = $this->pagetype->getPageTypePageTemplateDefaultPageObject();
    }

    public function view($ptID = false)
    {
        $this->setupPageType($ptID);
        $this->requireAsset('core/app/editable-fields');
        $this->set('pagetype', $this->pagetype);
        $this->set('defaultPage', $this->defaultPage);
        $category = AttributeKeyCategory::getByHandle('collection');
        $this->set('category', $category);
    }

    public function update_attribute($ptID = false)
    {
        $this->setupPageType($ptID);
        $sr = new EditResponse();
        if (Loader::helper('validation/token')->validate()) {
            $ak = CollectionKey::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['name']));
            if (is_object($ak)) {
                $ak->saveAttributeForm($this->defaultPage);
                $val = $this->defaultPage->getAttributeValueObject($ak);
            }
        } else {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
        if ($this->error->has()) {
            $sr->setError($this->error);
        } else {
            $sr->setMessage(t('Attribute saved successfully.'));
            $sr->setAdditionalDataAttribute('value',  $val->getValue('displaySanitized','display'));
        }
        $sr->outputJSON();
    }

    public function clear_attribute($ptID = false)
    {
        $this->setupPageType($ptID);
        $sr = new EditResponse();
        if (Loader::helper('validation/token')->validate()) {
            $ak = CollectionKey::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['akID']));
            if (is_object($ak)) {
                $this->defaultPage->clearAttribute($ak);
            }
        } else {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
        if ($this->error->has()) {
            $sr->setError($this->error);
        } else {
            $sr->setMessage(t('Attribute cleared successfully.'));
        }
        $sr->outputJSON();
    }

}