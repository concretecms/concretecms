<?php
namespace Concrete\Controller\Dialog\Area;
use Concrete\Core\Area\CustomStyle;
use Concrete\Core\Page\EditResponse;
use Concrete\Core\StyleCustomizer\Inline\StyleSet;
use Concrete\Controller\Backend\UserInterface\Page as BackendPageController;;

class Design extends BackendPageController {

    protected $viewPath = '/dialogs/area/design';

    public function on_start()
    {
        parent::on_start();
        $this->area = \Area::getOrCreate($this->page, $_REQUEST['arHandle']);
        $this->permissions = new \Permissions($this->area);
        $this->set('a', $this->area);
    }
    protected function canAccess()
    {
        return $this->permissions->canEditAreaDesign();
    }

    public function reset()
    {
        $a = $this->area;
        $nvc = $this->page->getVersionToModify();
        $nvc->resetAreaCustomStyle($a);
        $pr = new EditResponse();
        $pr->setPage($this->page);
        $pr->setAdditionalDataAttribute('aID', $this->area->getAreaID());
        $pr->setAdditionalDataAttribute('arHandle', $this->area->getAreaHandle());
        $pr->setMessage(t('Custom design reset.'));
        $pr->outputJSON();
    }

    public function submit()
    {
        if ($this->validateAction() && $this->canAccess()) {

            $a = $this->area;
            $oldStyle = $this->page->getAreaCustomStyle($a);
            if (is_object($oldStyle)) {
                $oldStyleSet = $oldStyle->getStyleSet();
            }

            $nvc = $this->page->getVersionToModify();

            $r = $this->request->request->all();
            $set = new StyleSet();
            $set->setBackgroundColor($r['backgroundColor']);
            $set->setBackgroundImageFileID(intval($r['backgroundImageFileID']));
            $set->setBackgroundRepeat($r['backgroundRepeat']);
            $set->setLinkColor($r['linkColor']);
            $set->setTextColor($r['textColor']);
            $set->setBaseFontSize($r['baseFontSize']);
            $set->setMarginTop($r['marginTop']);
            $set->setMarginRight($r['marginRight']);
            $set->setMarginBottom($r['marginBottom']);
            $set->setMarginLeft($r['marginLeft']);
            $set->setPaddingTop($r['paddingTop']);
            $set->setPaddingRight($r['paddingRight']);
            $set->setPaddingBottom($r['paddingBottom']);
            $set->setPaddingLeft($r['paddingLeft']);
            $set->setBorderWidth($r['borderWidth']);
            $set->setBorderStyle($r['borderStyle']);
            $set->setBorderColor($r['borderColor']);
            $set->setBorderRadius($r['borderRadius']);
            $set->setAlignment($r['alignment']);
            $set->setRotate($r['rotate']);
            $set->setBoxShadowBlur($r['boxShadowBlur']);
            $set->setBoxShadowColor($r['boxShadowColor']);
            $set->setBoxShadowHorizontal($r['boxShadowHorizontal']);
            $set->setBoxShadowVertical($r['boxShadowVertical']);
            $set->setBoxShadowSpread($r['boxShadowSpread']);
            $set->setCustomClass($r['customClass']);
            $set->save();

            $nvc->setCustomStyleSet($a, $set);

            $pr = new EditResponse();
            $pr->setPage($this->page);
            $pr->setAdditionalDataAttribute('aID', $this->area->getAreaID());
            $pr->setAdditionalDataAttribute('arHandle', $this->area->getAreaHandle());
            $pr->setAdditionalDataAttribute('issID', $set->getID());

            if (is_object($oldStyleSet)) {
                $pr->setAdditionalDataAttribute('oldIssID', $oldStyleSet->getID());
            }

            $style = new CustomStyle($set, $this->area->getAreaHandle());
            $pr->setAdditionalDataAttribute('css', $style->getCSS());
            $pr->setAdditionalDataAttribute('containerClass', $style->getContainerClass());

            $pr->setMessage(t('Design updated.'));
            $pr->outputJSON();
        }
    }

    public function action() {
        $url = call_user_func_array('parent::action', func_get_args());
        $url .= '&arHandle=' . $this->area->getAreaHandle();
        return $url;
    }

    public function view() {

	}

}

