<?
namespace Concrete\Controller\Dialog\Block;
use Concrete\Controller\Backend\UserInterface\Block as BackendInterfaceBlockController;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Page\EditResponse;
use Concrete\Core\Page\Style\Set;

class Design extends BackendInterfaceBlockController {

    protected $viewPath = '/dialogs/block/design';

    protected function canAccess()
    {
        return $this->permissions->canEditBlockDesign() || $this->permissions->canEditBlockCustomTemplate();
    }

    public function submit()
    {
        if ($this->validateAction() && $this->canAccess()) {

            $ax = $this->area;
            $cx = $this->page;
            if ($this->area->isGlobalArea()) {
                $ax = STACKS_AREA_NAME;
                $cx = \Stack::getByName($_REQUEST['arHandle']);
            }

            $b = \Block::getByID($_REQUEST['bID'], $cx, $ax);
            $nvc = $cx->getVersionToModify();
            if ($this->area->isGlobalArea()) {
                $xvc = $c->getVersionToModify(); // we need to create a new version of THIS page as well.
                $xvc->relateVersionEdits($nvc);
            }

            $b->loadNewCollection($nvc);

            //if this block is being changed, make sure it's a new version of the block.
            if ($b->isAlias()) {
                $nb = $b->duplicate($nvc);
                $b->deleteBlock();
                $b = $nb;
            }

            $r = $this->request->request->all();
            $set = new Set();
            $set->setBackgroundColor($r['backgroundColor']);
            $set->setBackgroundImageFileID(intval($r['backgroundImageFileID']));
            $set->setBackgroundRepeat($r['backgroundRepeat']);
            $set->setLinkColor($r['linkColor']);
            $set->setTextColor($r['textColor']);
            $set->setBaseFontSize($r['baseFontSize']);
            $set->save();

            $b->setCustomStyleSet($set);
            $pr = new EditResponse();
            $pr->setPage($this->page);
            $pr->setAdditionalDataAttribute('aID', $this->area->getAreaID());
            $pr->setAdditionalDataAttribute('arHandle', $this->area->getAreaHandle());
            $pr->setAdditionalDataAttribute('originalBlockID', $this->block->getBlockID());
            $pr->setAdditionalDataAttribute('bID', $b->getBlockID());
            $pr->setMessage(t('Custom design updated.'));
            $pr->outputJSON();
        }
    }

	public function view() {
        $btc = $this->block->getInstance();
        $btc->outputAutoHeaderItems();
        $bv = new BlockView($this->block);
        $bv->addScopeItems(array('c' => $this->page, 'a' => $this->area, 'dialogController' => $this));
        $this->set('bv', $bv);
	}

}

