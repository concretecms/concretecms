<?php
namespace Concrete\Core\Tree\Node\Type\Menu;


use Concrete\Core\Application\UserInterface\ContextMenu\Item\DialogLinkItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\DividerItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Menu;
use Concrete\Core\Tree\Node\Type\File;

class FileMenu extends Menu
{

    public function __construct(File $node)
    {
        parent::__construct();
        /*$p = new \Permissions($group);
        if ($p->canEditTreeNode()) {
            $url = \URL::to('/dashboard/users/groups', 'edit', $group->getTreeNodeGroupID());
            $this->addItem(new LinkItem($url, t('Edit Group')));
        }
        if ($p->canEditTreeNodePermissions()) {
            $this->addItem(new EditPermissionsItem($group));
        }
        if ($p->canDeleteTreeNode()) {
            $this->addItem(new DeleteItem($topic));
        }
        */

        $file = $node->getTreeNodeFileObject();
        if (!is_object($file)) {
            return false;
        }

        $this->addItem(new LinkItem('#', t('Clear'), ['data-file-manager-action' => 'clear']));
        $this->addItem(new DividerItem());

        $this->addItem(new DialogLinkItem(
                REL_DIR_FILES_TOOLS_REQUIRED . '/files/view?fID=' . $node->getTreeNodeFileID(),
            t('View'), t('View'), '90%', '75%')
        );
        $this->addItem(new LinkItem('#', t('Download'), [
            'data-file-manager-action' => 'download',
            'data-file-id' => $node->getTreeNodeFileID()
        ]));

        if ($file->canEdit()) {
            $this->addItem(new DialogLinkItem(
                    REL_DIR_FILES_TOOLS_REQUIRED . '/files/edit?fID=' . $node->getTreeNodeFileID(),
                    t('Edit'), t('Edit'), '90%', '75%')
            );
        }
        $this->addItem(new DialogLinkItem(
                \URL::to('/ccm/system/dialogs/file/thumbnails?fID=' . $node->getTreeNodeFileID()),
                t('Thumbnails'), t('Thumbnails'), '90%', '75%')
        );
        $this->addItem(new DialogLinkItem(
                \URL::to('/ccm/system/dialogs/file/properties?fID=' . $node->getTreeNodeFileID()),
                t('Properties'), t('Properties'), '850', '450')
        );
        $this->addItem(new DialogLinkItem(
                REL_DIR_FILES_TOOLS_REQUIRED . '/files/replace?fID=' . $node->getTreeNodeFileID(),
                t('Replace'), t('Replace'), '500', '200')
        );

        $this->addItem(new LinkItem('#', t('Duplicate'), [
            'data-file-manager-action' => 'duplicate',
            'data-file-id' => $node->getTreeNodeFileID()
        ]));

        $this->addItem(new DividerItem());

        $this->addItem(new DialogLinkItem(
                REL_DIR_FILES_TOOLS_REQUIRED . '/files/permissions?fID=' . $node->getTreeNodeFileID(),
                t('Permissions'), t('Permissions & Access'), '520', '450')
        );

        $this->addItem(new DialogLinkItem(
                REL_DIR_FILES_TOOLS_REQUIRED . '/files/delete?fID=' . $node->getTreeNodeFileID(),
                t('Delete'), t('Delete File'), '500', '350')
        );




    }

}