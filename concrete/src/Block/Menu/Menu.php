<?php
namespace Concrete\Core\Block\Menu;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\DialogLinkItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\DividerItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Tree\Menu\Item\DeleteItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Menu as ContextMenu;

class Menu extends ContextMenu
{

    protected $permissions;
    protected $block;
    protected $page;
    protected $area;

    /**
     * @return \Permissions
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @return Block
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return Area
     */
    public function getArea()
    {
        return $this->area;
    }

    public function __construct(Application $app, $config, Block $b, Page $c, Area $a)
    {
        parent::__construct();
        $p = new \Permissions($b);

        $this->permissions = $p;
        $this->block = $b;
        $this->page = $c;
        $this->area = $a;

        $this->setAttribute('data-block-menu', 'block-menu-b' . $b->getBlockID());
        $this->setAttribute('class', 'ccm-edit-mode-block-menu');

        $btw = $b->getBlockTypeObject();
        $btOriginal = $btw;
        $bID = $b->getBlockID();
        $aID = $a->getAreaID();
        $heightPlus = 20;
        $btHandle = $btw->getBlockTypeHandle();
        $editInline = false;
        if ($btw->supportsInlineEdit()) {
            $editInline = true;
        }
        if ($btw->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
            $_bi = $b->getInstance();
            $_bo = Block::getByID($_bi->getOriginalBlockID());
            $btOriginal = BlockType::getByHandle($_bo->getBlockTypeHandle());
            $btHandle = $btOriginal->getBlockTypeHandle();
            $heightPlus = 80;
            if ($btOriginal->supportsInlineEdit()) {
                $editInline = true;
            }
        }


        $canDesign = ($p->canEditBlockDesign() && $config->get('concrete.design.enable_custom') == true);
        $canModifyGroups = ($p->canEditBlockPermissions() && $config->get('concrete.permissions.model') != 'simple' && (!$a->isGlobalArea()));
        $canEditName = $p->canEditBlockName();
        $canEditCacheSettings = $p->canEditBlockCacheSettings();
        $canEditCustomTemplate = $p->canEditBlockCustomTemplate();
        $canScheduleGuestAccess = ($config->get('concrete.permissions.model') != 'simple' && $p->canGuestsViewThisBlock() && $p->canScheduleGuestAccess() && (!$a->isGlobalArea()));
        $canAliasBlockOut = ($c->isMasterCollection() && !$a->isGlobalArea());
        if ($canAliasBlockOut) {
            $ct = Type::getByID($c->getPageTypeID());
        }

        $isAlias = $b->isAlias();
        $u = new \User();
        $numChildren = (!$isAlias) ? $b->getNumChildren() : 0;
        if ($isAlias) {
            $deleteMessage = t('Do you want to delete this block?');
        } elseif ($numChildren) {
            $deleteMessage = t('Do you want to delete this block? This item is an original. If you delete it, you will delete all blocks aliased to it');
        } else {
            $deleteMessage = t('Do you want to delete this block?');
        }

        if ($btOriginal->getBlockTypeHandle() == BLOCK_HANDLE_STACK_PROXY) {
            if (is_object($_bo)) {
                $bi = $_bo->getInstance();
            } else {
                $bi = $b->getInstance();
            }

            $stack = \Stack::getByID($bi->stID);
            if (is_object($stack)) {
                $sp = new \Permissions($stack);
                if ($sp->canWrite()) {
                    $this->addItem(new LinkItem(\URL::to('/dashboard/blocks/stacks', 'view_details', $stack->getCollectionID()), t('Manage Stack Contents')));
                }
            }
        } else if ($p->canEditBlock() && $b->isEditable()) {
            if ($editInline) {
                if ($b->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) {
                    $this->addItem(new LinkItem('javascript:void(0)', t('Edit Layout'), [
                        'data-menu-action' => 'edit_inline',
                        'data-area-enable-grid-container' => $a->isGridContainerEnabled(),
                        'data-area-grid-maximum-columns' => $a->getAreaGridMaximumColumns()
                    ]));
                } else {
                    $this->addItem(new LinkItem('javascript:void(0)', t('Edit Block'), [
                        'data-menu-action' => 'edit_inline',
                        'data-area-enable-grid-container' => $a->isGridContainerEnabled(),
                        'data-area-grid-maximum-columns' => $a->getAreaGridMaximumColumns()
                    ]));
                }
            } else {
                $this->addItem(new LinkItem('javascript:void(0)', t('Edit Block'), [
                    'data-menu-action' => 'block_dialog',
                    'data-menu-href' => \URL::to('/ccm/system/dialogs/block/edit'),
                    'dialog-title' => t('Edit %s', t($btOriginal->getBlockTypeName())),
                    'dialog-width' => $btOriginal->getBlockTypeInterfaceWidth(),
                    'dialog-height' => $btOriginal->getBlockTypeInterfaceHeight() + $heightPlus
                ]));
            }
        }

        if ($b->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY && $b->getBlockTypeHandle() != BLOCK_HANDLE_PAGE_TYPE_OUTPUT_PROXY) {
            $this->addItem(new LinkItem('javascript:void(0)', t('Copy to Clipboard'), [
                'data-menu-action' => 'block_scrapbook',
                'data-token' => $app->make('token')->generate('tools/clipboard/to')
            ]));
        }

        if ($p->canDeleteBlock()) {
            $this->addItem(new LinkItem('javascript:void(0)', t('Delete'), [
                'data-menu-action' => 'delete_block',
                'data-menu-delete-message' => $deleteMessage,
            ]));
        }

        if ($b->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY) {
            if ($canDesign || $canEditCustomTemplate || $canEditName || $canEditCacheSettings) {
                $this->addItem(new DividerItem());
                if ($canDesign || $canEditCustomTemplate) {
                    $this->addItem(new LinkItem('#', t('Design &amp; Custom Template'), [
                        'data-menu-action' => 'block_design',
                    ]));
                }

                if ($b->getBlockTypeHandle() != BLOCK_HANDLE_PAGE_TYPE_OUTPUT_PROXY && ($canEditName || $canEditCacheSettings)) {
                    $this->addItem(new LinkItem('#', t('Advanced'), [
                        'dialog-title' => t('Advanced Block Settings'),
                        'data-menu-action' => 'block_dialog',
                        'data-menu-href' => \URL::to('/ccm/system/dialogs/block/cache'),
                        'dialog-width' => 500,
                        'dialog-height' => 320
                    ]));
                }
            }
        }

        if ($b->getBlockTypeHandle() != BLOCK_HANDLE_PAGE_TYPE_OUTPUT_PROXY && ($canModifyGroups || $canScheduleGuestAccess || $canAliasBlockOut)) {
            $this->addItem(new DividerItem());
            if ($canModifyGroups) {
                $this->addItem(new LinkItem('#', t('Permissions'), [
                    'dialog-title' => t('Block Permissions'),
                    'data-menu-action' => 'block_dialog',
                    'data-menu-href' => \URL::to('/ccm/system/dialogs/block/permissions/list'),
                    'dialog-width' => 350,
                    'dialog-height' => 450
                ]));
            }

            if ($canScheduleGuestAccess) {
                $this->addItem(new LinkItem('#', t('Schedule Guest Access'), [
                    'dialog-title' => t('Schedule Guest Access'),
                    'data-menu-action' => 'block_dialog',
                    'data-menu-href' => \URL::to('/ccm/system/dialogs/block/permissions/guest_access'),
                    'dialog-width' => 500,
                    'dialog-height' => 320
                ]));
            }

            if ($canAliasBlockOut) {
                $this->addItem(new LinkItem('#', t('Setup on Child Pages'), [
                    'dialog-title' => t('Setup on Child Pages'),
                    'data-menu-action' => 'block_dialog',
                    'data-menu-href' => \URL::to('/ccm/system/dialogs/block/aliasing'),
                    'dialog-width' => 500,
                    'dialog-height' => 'auto'
                ]));
            }
        }
    }
}