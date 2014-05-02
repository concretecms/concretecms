<?
namespace Concrete\Controller\Panel\Page\Design;
use \Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
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
        $pt = PageTheme::getByID($pThemeID);
        if (is_object($pt) && $pt->isThemeCustomizable()) {
            $presets = $pt->getThemeCustomizableStylePresets();
            foreach($presets as $preset) {
                if ($preset->isDefaultPreset()) {
                    $selectedPreset = $preset;
                }
            }

            if ($this->request->request->has('handle')) {
                $preset = $pt->getThemeCustomizablePreset($this->request->request->get('handle'));
                if (is_object($preset)) {
                    $selectedPreset = $preset;
                }
            }

            // finally, we sort the presets so that the selected
            // preset is at the top
            usort($presets, function($a, $b) use ($selectedPreset) {
                if ($selectedPreset->getPresetHandle() == $a->getPresetHandle()) {
                    return -1;
                }
                if ($selectedPreset->getPresetHandle() == $b->getPresetHandle()) {
                    return 1;
                }

                return strcasecmp($a->getPresetName(), $b->getPresetName());
            });

            $styleList = $pt->getThemeCustomizableStyleList();
            $valueList = $pt->getThemeCustomStyleValueList();
            if (!is_object($valueList) || $this->request->request->has('handle')) {
                $valueList = $selectedPreset->getStyleValueList();
            }
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
        // now we loop through all the styles and get values from the post.
        $values = array();
        $vl = new \Concrete\Core\StyleCustomizer\Style\ValueList();
        foreach($styles->getSets() as $set) {
            foreach($set->getStyles() as $style) {
                $value = $style->getValueFromRequest($this->request->request);
                if (is_object($value)) {
                    $vl->addValue($value);
                }
            }
        }
        return $vl;
    }

    public function preview($pThemeID) {
        $vl = $this->getValueListFromRequest($pThemeID);
        $pt = PageTheme::getByID($pThemeID);
        $pt->setStylesheetCacheRelativePath(REL_DIR_FILES_CACHE . '/preview');
        $pt->setStylesheetCachePath(DIR_FILES_CACHE . '/preview');
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
            $pt->saveThemeCustomValueList($vl);
            $r = new PageEditResponse();
            $r->setPage($this->page);
            $r->setRedirectURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $this->page->getCollectionID());
            $r->outputJSON();
        }
    }

    /*
    public function apply_to_page($pThemeID) {
        if ($this->validateAction()) {
            $pt = PageTheme::getByID($pThemeID);
            $nvc = $this->page->getVersionToModify();
            $values = $pt->mergeStylesFromPost($_POST);
            $nvc->updateCustomThemeStyles($values);
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
            $pt = PageTheme::getByID($pThemeID);
            $pt->reset();
            $r = new PageEditResponse();
            $r->setPage($this->page);
            $r->setRedirectURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $this->page->getCollectionID());
            $r->outputJSON();
        }
    }

    */

}