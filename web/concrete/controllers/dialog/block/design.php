<?php
namespace Concrete\Controller\Dialog\Block;
use Concrete\Controller\Backend\UserInterface\Block as BackendInterfaceBlockController;
use Concrete\Core\Block\CustomStyle;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Page\EditResponse;
use Concrete\Core\StyleCustomizer\Inline\StyleSet;

class Design extends BackendInterfaceBlockController {

    protected $viewPath = '/dialogs/block/design';

    protected function canAccess()
    {
        return $this->permissions->canEditBlockDesign()
        || $this->permissions->canEditBlockCustomTemplate();
    }

    public function reset()
    {
        $b = $this->getBlockToEdit();
        $b->resetCustomStyle();
        $pr = new EditResponse();
        $pr->setPage($this->page);
        $pr->setAdditionalDataAttribute('aID', $this->area->getAreaID());
        $pr->setAdditionalDataAttribute('arHandle', $this->area->getAreaHandle());
        $pr->setAdditionalDataAttribute('originalBlockID', $this->block->getBlockID());
        $pr->setAdditionalDataAttribute('bID', $b->getBlockID());
        $pr->setMessage(t('Custom design reset.'));
        $pr->outputJSON();
    }

    public function submit()
    {
        if ($this->validateAction() && $this->canAccess()) {

            $b = $this->getBlockToEdit();
            $oldStyle = $b->getCustomStyle();
            if (is_object($oldStyle)) {
                $oldStyleSet = $oldStyle->getStyleSet();
            }

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

            $b->setCustomStyleSet($set);

            if ($this->permissions->canEditBlockCustomTemplate()) {
                $data = array();
                $data['bFilename'] = $r['bFilename'];
                $b->updateBlockInformation($data);
            }

            $pr = new EditResponse();
            $pr->setPage($this->page);
            $pr->setAdditionalDataAttribute('aID', $this->area->getAreaID());
            $pr->setAdditionalDataAttribute('arHandle', $this->area->getAreaHandle());
            $pr->setAdditionalDataAttribute('originalBlockID', $this->block->getBlockID());
            $pr->setAdditionalDataAttribute('issID', $set->getID());

            if (is_object($oldStyleSet)) {
                $pr->setAdditionalDataAttribute('oldIssID', $oldStyleSet->getID());
            }

            $style = new CustomStyle($set, $b->getBlockID(), $this->area->getAreaHandle());

            $pr->setAdditionalDataAttribute('css', $style->getCSS());
            $pr->setAdditionalDataAttribute('bID', $b->getBlockID());
            $pr->setMessage(t('Design updated.'));
            $pr->outputJSON();
        }
    }

	public function view() {
        $btc = $this->block->getInstance();
        $btc->outputAutoHeaderItems();
        $bv = new BlockView($this->block);
        $bv->addScopeItems(array('c' => $this->page, 'a' => $this->area, 'dialogController' => $this));
        $this->set('bv', $bv);

        $canEditCustomTemplate = false;
        if ($this->permissions->canEditBlockCustomTemplate()) {
            $canEditCustomTemplate = true;
            if ($this->block->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
                $bi = $this->block->getInstance();
                $bx = \Block::getByID($bi->getOriginalBlockID());
                $bt = \BlockType::getByID($bx->getBlockTypeID());
            } else {
                $bt = \BlockType::getByID($this->block->getBlockTypeID());
            }
            $templates = $bt->getBlockTypeCustomTemplates();
            $this->set('templates', $templates);
        }
        $this->set('canEditCustomTemplate', $canEditCustomTemplate);
	}

}

