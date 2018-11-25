<?php
namespace Concrete\Controller\Panel\Detail\Page;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Attribute\Context\AttributePanelContext;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Workflow\Request\ApprovePageRequest;
use Concrete\Core\Page\EditResponse as PageEditResponse;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\User\User;
use Concrete\Core\Page\Collection\Version\Version;
use Concrete\Core\Attribute\Key\CollectionKey as CollectionAttributeKey;
use Concrete\Core\Entity\Attribute\Key\PageKey;
use Concrete\Core\Attribute\View as AttributeTypeView;
use stdClass;

class Attributes extends BackendInterfacePageController
{
    protected $viewPath = '/panels/details/page/attributes';
    // we need this extra because this controller gets called by another page
    // and that page needs to know how to submit it.
    protected $controllerActionPath = '/ccm/system/panels/details/page/attributes';

    protected function canAccess()
    {
        return $this->permissions->canEditPageProperties();
    }

    public function on_start()
    {
        parent::on_start();
        $pk = PermissionKey::getByHandle('edit_page_properties');
        $pk->setPermissionObject($this->page);
        $this->assignment = $pk->getMyAssignment();
    }

    protected function getAttributeJSONRepresentation(PageKey $ak, $mode = 'edit')
    {
        ob_start();
        $av = new AttributeTypeView($ak);
        if ($mode == 'edit') {
            $caValue = $this->page->getAttributeValueObject($ak);
            $ak->render(new AttributePanelContext(), $caValue);
        } else {
            echo $av->render(new AttributePanelContext());
        }
        $html = ob_get_contents();
        ob_end_clean();
        $obj = new stdClass();
        $obj->controlID = $ak->getController()->getControlID();
        $obj->akID = $ak->getAttributeKeyID();
        $obj->label = $ak->getAttributeKeyDisplayName();
        $obj->content = $html;
        $obj->pending = ($mode == 'add') ? true : false;

        return $obj;
    }

    public function view()
    {
        $this->set('assignment', $this->assignment);
        $this->set('dt', $this->app->make('helper/form/date_time'));
        $this->set('uh', $this->app->make('helper/form/user_selector'));
        $selectedAttributes = [];
        $allowed = $this->assignment->getAttributesAllowedArray();
        foreach ($this->page->getSetCollectionAttributes() as $ak) {
            if (is_object($ak) && in_array($ak->getAttributeKeyID(), $allowed)) {
                $obj = $this->getAttributeJSONRepresentation($ak);
                $selectedAttributes[] = $obj;
            }
        }
        $this->set('selectedAttributes', $this->app->make('helper/json')->encode($selectedAttributes));
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $post = $this->request->request;
            $c = $this->page;
            $asl = $this->assignment;
            $nvc = $c->getVersionToModify();

            // First, we check out the attributes we need to clear.
            $setAttribs = $nvc->getSetCollectionAttributes();
            $processedAttributes = [];
            $attributesToValidate = [];
            $attributesToClear = [];
            $selectedAKIDs = $post->get('selectedAKIDs');
            if (!is_array($selectedAKIDs)) {
                $selectedAKIDs = [];
            }
            $selected = is_array($post->get('selectedAKIDs')) ? $post->get('selectedAKIDs') : [];

            foreach ($setAttribs as $ak) {
                // do I have the ability to edit this attribute?
                if (in_array($ak->getAttributeKeyID(), $asl->getAttributesAllowedArray())) {
                    // Is this item in the selectedAKIDs array? If so then it is being saved
                    if (in_array($ak->getAttributeKeyID(), $selected)) {
                        $attributesToValidate[] = $ak;
                    } else {
                        // it is being removed
                        $attributesToClear[] = $ak;
                    }
                    $processedAttributes[] = $ak->getAttributeKeyID();
                }
            }
            $newAttributes = array_diff($selectedAKIDs, $processedAttributes);
            foreach ($newAttributes as $akID) {
                if ($akID > 0 && in_array($akID, $asl->getAttributesAllowedArray())) {
                    $ak = CollectionAttributeKey::getByID($akID);
                    $controller = $ak->getController();
                    $attributesToValidate[] = $controller;
                }
            }

            // Check if attributes have a valid value
            $e = $this->app->make('error');
            foreach ($attributesToValidate as $attribute) {
                $controller = $attribute->getController();
                $data = $controller->request();
                $error = $controller->validateForm($data);
                if ($error !== true) {
                    $e->add($error);
                }
            }
            if ($e->has()) {
                $r = new PageEditResponse();
                $r->setError($e);
                $r->outputJSON();
            } else {

                // Update the page version after everything is validated
                $data = [];
                if ($asl->allowEditName()) {
                    $data['cName'] = $post->get('cName');
                }
                if ($asl->allowEditDescription()) {
                    $data['cDescription'] = $post->get('cDescription');
                }
                if ($asl->allowEditDateTime()) {
                    $dt = $this->app->make('helper/form/date_time');
                    $data['cDatePublic'] = $dt->translate('cDatePublic');
                }
                if ($asl->allowEditUserID()) {
                    $data['uID'] = $post->get('uID');
                }
                $nvc->update($data);

                // Save valid attributes
                foreach ($attributesToValidate as $attribute) {
                    $controller = $attribute->getController();
                    $value = $controller->createAttributeValueFromRequest();
                    $nvc->setAttribute($attribute, $value);
                }

                // Clear attributes that need to be removed
                foreach ($attributesToClear as $attribute) {
                    $nvc->clearAttribute($attribute);
                }

                $config = $this->app->make('config');
                if ($this->request->request->get('sitemap') && $this->permissions->canApprovePageVersions() && $config->get('concrete.misc.sitemap_approve_immediately')) {
                    $pkr = new ApprovePageRequest();
                    $u = new User();
                    $pkr->setRequestedPage($this->page);
                    $v = Version::get($this->page, 'RECENT');
                    $pkr->setRequestedVersionID($v->getVersionID());
                    $pkr->setRequesterUserID($u->getUserID());
                    $response = $pkr->trigger();
                    $u->unloadCollectionEdit();
                }

                $r = new PageEditResponse();
                $r->setPage($c);
                $r->setTitle(t('Page Updated'));
                $r->setMessage(t('Page Attributes have been saved.'));
                $r->outputJSON();
            }
        }
    }

    /**
     * Retrieve attribute HTML to inject into the other view.
     */
    public function add_attribute()
    {
        $allowed = $this->assignment->getAttributesAllowedArray();
        $ak = CollectionAttributeKey::getByID($_REQUEST['akID']);
        if (is_object($ak) && in_array($ak->getAttributeKeyID(), $allowed)) {
            $obj = $this->getAttributeJSONRepresentation($ak, 'add');
            $obj->pending = true;
            $obj->assets = [];
            $ag = ResponseAssetGroup::get();
            foreach ($ag->getAssetsToOutput() as $position => $assets) {
                foreach ($assets as $asset) {
                    if (is_object($asset)) {
                        // have to do a check here because we might be included a dumb javascript call like i18n_js
                        $obj->assets[$asset->getAssetType()][] = $asset->getAssetURL();
                    }
                }
            }
            $this->app->make('helper/ajax')->sendResult($obj);
        }
    }
}
