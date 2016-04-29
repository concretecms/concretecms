<?php
namespace Concrete\Core\File;

use Concrete\Core\Application\UserInterface\ContextMenu\Item\DialogLinkItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\DividerItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;

class Menu extends \Concrete\Core\Application\UserInterface\ContextMenu\Menu
{

    public function __construct(File $file)
    {
        parent::__construct();

        $this->addItem(new LinkItem('#', t('Clear'), ['data-file-manager-action' => 'clear']));
        $this->addItem(new DividerItem());

        $this->addItem(new DialogLinkItem(
                REL_DIR_FILES_TOOLS_REQUIRED . '/files/view?fID=' . $file->getFileID(),
                t('View'), t('View'), '90%', '75%')
        );
        $this->addItem(new LinkItem('#', t('Download'), [
            'data-file-manager-action' => 'download',
            'data-file-id' => $file->getFileID()
        ]));

        if ($file->canEdit()) {
            $this->addItem(new DialogLinkItem(
                    REL_DIR_FILES_TOOLS_REQUIRED . '/files/edit?fID=' . $file->getFileID(),
                    t('Edit'), t('Edit'), '90%', '75%')
            );
        }
        $this->addItem(new DialogLinkItem(
                \URL::to('/ccm/system/dialogs/file/thumbnails?fID=' . $file->getFileID()),
                t('Thumbnails'), t('Thumbnails'), '90%', '75%')
        );
        $this->addItem(new DialogLinkItem(
                \URL::to('/ccm/system/dialogs/file/properties?fID=' . $file->getFileID()),
                t('Properties'), t('Properties'), '850', '450')
        );
        $this->addItem(new DialogLinkItem(
                REL_DIR_FILES_TOOLS_REQUIRED . '/files/replace?fID=' . $file->getFileID(),
                t('Replace'), t('Replace'), '500', '200')
        );

        $this->addItem(new LinkItem('#', t('Duplicate'), [
            'data-file-manager-action' => 'duplicate',
            'data-file-id' => $file->getFileID()
        ]));

        $this->addItem(new DividerItem());

        $this->addItem(new DialogLinkItem(
                REL_DIR_FILES_TOOLS_REQUIRED . '/files/permissions?fID=' . $file->getFileID(),
                t('Permissions'), t('Permissions & Access'), '520', '450')
        );

        $this->addItem(new DialogLinkItem(
                REL_DIR_FILES_TOOLS_REQUIRED . '/files/delete?fID=' . $file->getFileID(),
                t('Delete'), t('Delete File'), '500', '350')
        );




    }
}
