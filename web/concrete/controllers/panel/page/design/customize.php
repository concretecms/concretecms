<?php
namespace Concrete\Controller\Panel\Page\Design;
use \Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Page\PageList;
use Permissions;
use Page;
use stdClass;
use PermissionKey;
use PageTheme;
use PageEditResponse;
use Request;
use Loader;
use User;
use Response;
use Core;

class Customize extends BackendInterfacePageController {

    protected $viewPath = '/panels/page/design/customize';
    protected $controllerActionPath = '/ccm/system/panels/page/design/customize';
    protected $helpers = array('form');

    public function canAccess() {
        return $this->permissions->canEditPageTheme();
    }

    public function view($pThemeID) {
        $this->requireAsset('core/style-customizer');
        $pt = PageTheme::getByID($pThemeID);
        if (is_object($pt) && $pt->isThemeCustomizable()) {

            $presets = $pt->getThemeCustomizableStylePresets();
            foreach($presets as $preset) {
                if ($preset->isDefaultPreset()) {
                    $defaultPreset = $preset;
                }
            }

            // load the defaults for the panel, before we get to any
            // request-based tomfoolery.
            if ($this->page->hasPageThemeCustomizations()) {
                $object = $this->page->getCustomStyleObject();
            } else {
                $object = $pt->getThemeCustomStyleObject();
            }

            if (is_object($object)) {
                $handle = $object->getPresetHandle();
                if ($handle) {
                    $selectedPreset = $pt->getThemeCustomizablePreset($handle);
                }
                $valueList = $object->getValueList();
                $sccRecord = $object->getCustomCssRecord();
            } else {
                $selectedPreset = $defaultPreset;
                $valueList = $defaultPreset->getStyleValueList();
            }

            if ($this->request->request->has('handle')) {
                $preset = $pt->getThemeCustomizablePreset($this->request->request->get('handle'));
                if (is_object($preset)) {
                    $selectedPreset = $preset;
                    $valueList = $preset->getStyleValueList();
                }
            }

            // finally, we sort the presets so that the selected
            // preset is at the top
            usort($presets, function($a, $b) use ($selectedPreset) {
                if (!$selectedPreset) {
                    return -1;
                }
                if ($selectedPreset->getPresetHandle() == $a->getPresetHandle()) {
                    return -1;
                }
                if ($selectedPreset->getPresetHandle() == $b->getPresetHandle()) {
                    return 1;
                }

                return strcasecmp($a->getPresetDisplayName('text'), $b->getPresetDisplayName('text'));
            });

            $styleList = $pt->getThemeCustomizableStyleList();
            $sccRecordID = 0;
            if ($sccRecord) {
                $sccRecordID = $sccRecord->getRecordID();
            }
            $this->set('sccRecordID', $sccRecordID);
            $this->set('valueList', $valueList);
            $this->set('presets', $presets);
            $this->set('selectedPreset', $selectedPreset);
            $this->set('styleSets', $styleList->getSets());
            $this->set('theme', $pt);
        } else {
            throw new \Exception(t('Invalid or non-customizable theme.'));
        }
    }

    protected function getValueListFromRequest($pThemeID) {
        $pt = PageTheme::getByID($pThemeID);
        $styles = $pt->getThemeCustomizableStyleList();
        $vl = \Concrete\Core\StyleCustomizer\Style\ValueList::loadFromRequest($this->request->request, $styles);
        return $vl;
    }

    public function preview($pThemeID) {
        $vl = $this->getValueListFromRequest($pThemeID);
        $pt = PageTheme::getByID($pThemeID);
        $pt->enablePreviewRequest();
        $sheets = $pt->getThemeCustomizableStyleSheets();
        // for each customizable stylesheet in the theme, we take the value list
        // and send its variables through the LESS parser.
        foreach($sheets as $sheet) {
            $sheet->setValueList($vl);
            // we save each sheet to the preview location.
            $sheet->output();
        }

        // and finally, we pass our modified theme into the updated view, which we send back in the iframe.
        $req = Request::getInstance();
        $req->setCurrentPage($this->page);
        $controller = $this->page->getPageController();
        $view = $controller->getViewObject();
        $view->setCustomPageTheme($pt);
        $req->setCustomRequestUser(-1);
        $response = new Response();
        $content = $view->render();
        $response->setContent($content);
        return $response;
    }

    public function apply_to_site($pThemeID) {
        $pk = PermissionKey::getByHandle('customize_themes');
        if ($this->validateAction() && $pk->can()) {
            $vl = $this->getValueListFromRequest($pThemeID);
            $pt = PageTheme::getByID($pThemeID);
            $vl->save();
            $sccRecord = false;
            if ($this->request->request->has('sccRecordID')) {
                $sccRecord = \Concrete\Core\StyleCustomizer\CustomCssRecord::getByID($this->request->request->get('sccRecordID'));
            }
            $preset = false;
            if ($this->request->request->has('handle')) {
                $preset = $pt->getThemeCustomizablePreset($this->request->request->get('handle'));
            }

            // reset all custom styles on particular pages
            $pl = new PageList();
            $pl->filterByPagesWithCustomStyles();
            $results = $pl->getResults();
            foreach($results as $csc) {
                $cscv = $csc->getVersionToModify();
                $cscv->resetCustomThemeStyles();
                $vo = $csc->getVersionObject();
                if ($vo->isApproved()) {
                    $vo = $cscv->getVersionObject();
                    $vo->approve();
                }
            }

            // set the global style object.
            $pt->setCustomStyleObject($vl, $preset, $sccRecord);
            $r = new PageEditResponse();
            $r->setPage($this->page);
            $r->setRedirectURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $this->page->getCollectionID());
            $r->outputJSON();
        }
    }


    public function apply_to_page($pThemeID) {
        if ($this->validateAction()) {
            $vl = $this->getValueListFromRequest($pThemeID);
            $pt = PageTheme::getByID($pThemeID);
            $vl->save();
            $sccRecord = false;
            if ($this->request->request->has('sccRecordID')) {
                $sccRecord = \Concrete\Core\StyleCustomizer\CustomCssRecord::getByID($this->request->request->get('sccRecordID'));
            }
            $preset = false;
            if ($this->request->request->has('handle')) {
                $preset = $pt->getThemeCustomizablePreset($this->request->request->get('handle'));
            }

            $nvc = $this->page->getVersionToModify();
            $nvc->setCustomStyleObject($pt, $vl, $preset, $sccRecord);

            $r = new PageEditResponse();
            $r->setPage($this->page);
            $r->setRedirectURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $this->page->getCollectionID());
            $r->outputJSON();
        }
    }

    public function reset_page_customizations() {
        if ($this->validateAction()) {
            $nvc = $this->page->getVersionToModify();
            $nvc->resetCustomThemeStyles();
            $r = new PageEditResponse();
            $r->setPage($this->page);
            $r->setRedirectURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $this->page->getCollectionID());
            $r->outputJSON();
        }
    }

    public function reset_site_customizations($pThemeID) {
        if ($this->validateAction()) {
            Page::resetAllCustomStyles();

            $pt = PageTheme::getByID($pThemeID);
            $pt->resetThemeCustomStyles();

            $r = new PageEditResponse();
            $r->setPage($this->page);
            $r->setRedirectURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $this->page->getCollectionID());
            $r->outputJSON();
        }
    }
}