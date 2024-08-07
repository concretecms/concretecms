<?php
namespace Concrete\Controller\Dialog\Page;

use Concrete\Core\Area\Area;
use Block;
use BlockType;
use Concrete\Core\Block\Events\BlockAdd;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Block\View\BlockView;
use Events;
use Exception;
use Loader;
use PageEditResponse;
use Permissions;
use Stack;
use Concrete\Core\User\User;

class AddBlock extends BackendInterfacePageController
{
    protected $viewPath = '/dialogs/page/add_block';

    /**
     * @deprecated What's deprecated is the "public" part.
     *
     * @var \Concrete\Core\Area\Area
     */
    public $area;

    public function on_start()
    {
        parent::on_start();
        $request = $this->request;

        if (!Loader::helper('validation/numbers')->integer($_REQUEST['btID'])) {
            throw new Exception(t('Access Denied'));
        }

        $this->blockType = BlockType::getByID($_REQUEST['btID']);
        $this->area = Area::get($this->page, $_REQUEST['arHandle']);
        $this->pageToModify = $this->page;
        $this->areaToModify = $this->area;
        if ($this->area->isGlobalArea()) {
            $this->pageToModify = Stack::getByName($_REQUEST['arHandle']);
            $this->areaToModify = Area::get($this->pageToModify, STACKS_AREA_NAME);
        }
        $this->areaPermissions = new Permissions($this->areaToModify);
        $cnt = $this->blockType->getController();
        if (!is_a($cnt, '\Concrete\Core\Block\BlockController')) {
            throw new Exception(t(
                                    'Unable to load the controller for this block type. Perhaps it has been moved or removed.'));
        }
        $this->blockTypeController = $cnt;
        if (isset($_REQUEST['arCustomTemplates']) && is_array($_REQUEST['arCustomTemplates'])) {
            foreach ($_REQUEST['arCustomTemplates'] as $btHandle => $template) {
                $this->area->setCustomTemplate($btHandle, $template);
            }
        }
    }

    public function view()
    {
        $bv = new BlockView($this->blockType);
        $bv->setAreaObject($this->area);
        // Handle special posted area parameters here
        if (isset($_REQUEST['arGridMaximumColumns'])) {
            $this->area->setAreaGridMaximumColumns(intval($_REQUEST['arGridMaximumColumns']));
        }
        if (isset($_REQUEST['arEnableGridContainer']) && $_REQUEST['arEnableGridContainer'] == 1) {
            $this->area->enableGridContainer();
        }
        if (isset($_REQUEST['arCustomTemplates']) && is_array($_REQUEST['arCustomTemplates'])) {
            foreach ($_REQUEST['arCustomTemplates'] as $btHandle => $template) {
                $this->area->setCustomTemplate($btHandle, $template);
            }
        }
        $bv->addScopeItems(array('a' => isset($this->areaToModify) ? $this->areaToModify : null, 'cp' => $this->permissions, 'ap' => $this->areaPermissions));
        $this->set('blockView', $bv);
        $this->set('blockType', $this->blockType);
        $this->set('btHandle', $this->blockType->getBlockTypeHandle());
        $this->set("blockTypeController", $this->blockTypeController);
        $this->set('area', $this->area);
    }

    public function submit()
    {
        $pc = new PageEditResponse($this->error);
        $pc->setPage($this->page);
        if (is_object($this->blockType) && !$this->blockType->hasAddTemplate()) {
            $token = $this->app->make('token');
            if (!$token->validate()) {
                $this->error->add($token->getErrorMessage());
            }
        } else {
            $this->validateAction();
        }
        if (!$this->error->has()) {
            $data = $_POST;
            $bt = $this->blockType;
            $u = $this->app->make(User::class);
            $data['uID'] = $u->getUserID();

            $e = $this->blockTypeController->validate($data);
            if ((!is_object($e)) || (($e instanceof \Concrete\Core\Error\ErrorList\ErrorList) && (!$e->has()))) {
                if (!$bt->includeAll()) {
                    $nvc = $this->pageToModify->getVersionToModify();
                    $nb = $nvc->addBlock($bt, $this->areaToModify, $data);
                } else {
                    // if we apply to all, then we don't worry about a new version of the page
                    $nb = $this->pageToModify->addBlock($bt, $this->areaToModify, $data);
                }

                $event = new BlockAdd($nb, $this->pageToModify);
                Events::dispatch('on_block_add', $event);

                if ($this->area->isGlobalArea() && $nvc instanceof Collection) {
                    $xvc = $this->page->getVersionToModify(); // we need to create a new version of THIS page as well.
                    $xvc->relateVersionEdits($nvc);
                }

                $db = null;
                // now we check to see if there's a block in this area that we are adding it after.
                if ($_REQUEST['dragAreaBlockID'] > 0 && Loader::helper('validation/numbers')->integer(
                                                              $_REQUEST['dragAreaBlockID'])
                ) {
                    $db = Block::getByID($_REQUEST['dragAreaBlockID'], $this->pageToModify, $this->areaToModify);
                    if (is_object($db) && !$db->isError()) {
                        $nb->moveBlockToDisplayOrderPosition($db);
                    }
                }
                if (!is_object($db)) {
                    $nb->moveBlockToDisplayOrderPosition(false);
                }

                $pc->setAdditionalDataAttribute('btID', $nb->getBlockTypeID());
                $pc->setAdditionalDataAttribute('bID', $nb->getBlockID());
                $pc->setAdditionalDataAttribute('arHandle', $this->area->getAreaHandle());

                $pc->setAdditionalDataAttribute('aID', $this->area->getAreaID());
            } else {
                $pc->setError($e);
            }
        }
        $pc->outputJSON();
    }

    protected function canAccess()
    {
        return $this->areaPermissions->canAddBlock($this->blockType);
    }
}
