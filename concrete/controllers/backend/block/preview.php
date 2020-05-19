<?php

namespace Concrete\Controller\Backend\Block;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Permission\Checker as Permissions;
use Concrete\Core\View\View;

class Preview extends BackendInterfacePageController
{
    protected $viewPath = '/backend/block/preview';

    /**
     * @var Block
     */
    protected $block;

    /**
     * @var \Concrete\Core\Page\Page|Stack
     */
    protected $stack;

    public function __construct()
    {
        parent::__construct();

        $this->view = new View($this->viewPath);
        $this->view->setViewTemplate('block_preview.php');
    }

    public function on_start()
    {
        parent::on_start();

        $r = $this->request;
        if (!$sID = $r->query->get('sID')) {
            $sID = $r->request->get('sID');
        }

        $stack = $sID ? Stack::getByID((int) $sID) : null;
        if (!is_object($stack)) {
            throw new UserMessageException(t('Unable to find the specified stack.'));
        }

        $r = $this->request;
        if (!$bID = $r->query->get('bID')) {
            $bID = $r->request->get('bID');
        }

        $b = Block::getByID($bID, $stack, STACKS_AREA_NAME);
        if (!$b) {
            throw new UserMessageException(t('Access Denied'));
        }

        $this->block = $b;
        $this->stack = $stack;
    }

    public function render()
    {
        $loc = Localization::getInstance();
        $loc->setActiveContext(Localization::CONTEXT_SITE);
        $this->app['multilingual/detector']->setupSiteInterfaceLocalization($this->page);

        if (is_object($csr = $this->block->getCustomStyle())) {
            $css = $csr->getCSS();
            if ($css !== '') {
                $styleHeader = $csr->getStyleWrapper($css);
                $this->view->addHeaderItem($styleHeader);
            }
        }

        $bv = new BlockView($this->block);
        $bv->addScopeItems(['c' => $this->page]);
        $bv->disableControls();
        $this->set('bv', $bv);

        $this->setTheme($this->page->getCollectionThemeObject());
    }

    protected function canAccess()
    {
        return $this->permissions->canEditPageContents()
            && (new Permissions($this->stack))->canRead()
            && (new Permissions($this->block))->canViewBlock();
    }
}
