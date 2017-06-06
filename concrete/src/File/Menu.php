<?php
namespace Concrete\Core\File;

use Concrete\Core\Application\UserInterface\ContextMenu\Item\DialogLinkItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\DividerItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\Tree\Menu\Item\DeleteItem;

class Menu extends \Concrete\Core\Application\UserInterface\ContextMenu\Menu
{

    protected $menuAttributes = ['class' => 'ccm-popover-file-menu'];
    protected $minItemThreshold = 2; // because we already have clear and the divider, we just hide them with JS

    public function __construct(FileEntity $file)
    {
        parent::__construct();

        $this->setAttribute('data-search-file-menu', $file->getFileID());
        $this->addItem(new LinkItem('#', t('Clear'), ['data-file-manager-action' => 'clear']));
        $this->addItem(new DividerItem());

        $fp = new \Permissions($file);
        if ($fp->canViewFile() && $file->canView()) {

            $this->addItem(new DialogLinkItem(
                    REL_DIR_FILES_TOOLS_REQUIRED . '/files/view?fID=' . $file->getFileID(),
                    t('View'), t('View'), '90%', '75%')
            );
        }

        if ($fp->canViewFile() && $file->canView()) {
            $this->addItem(new LinkItem('#', t('Download'), [
                'data-file-manager-action' => 'download',
                'data-file-id' => $file->getFileID()
            ]));
        }

        if ($file->canEdit() && $fp->canEditFileContents()) {
            $this->addItem(new DialogLinkItem(
                    REL_DIR_FILES_TOOLS_REQUIRED . '/files/edit?fID=' . $file->getFileID(),
                    t('Edit'), t('Edit'), '90%', '75%')
            );
            $this->addItem(new DialogLinkItem(
                    \URL::to('/ccm/system/dialogs/file/thumbnails?fID=' . $file->getFileID()),
                    t('Thumbnails'), t('Thumbnails'), '90%', '75%')
            );
        }
        if ($fp->canViewFileInFileManager()) {
            $this->addItem(new DialogLinkItem(
                    \URL::to('/ccm/system/dialogs/file/properties?fID=' . $file->getFileID()),
                    t('Properties'), t('Properties'), '850', '450')
            );
        }
        if ($fp->canEditFileContents()) {
            $this->addItem(new DialogLinkItem(
                    REL_DIR_FILES_TOOLS_REQUIRED . '/files/replace?fID=' . $file->getFileID(),
                    t('Replace'), t('Replace'), '500', '200')
            );
        }
        $this->addItem(new DialogLinkItem(
                \URL::to('/ccm/system/dialogs/file/folder?fID=' . $file->getFileID()),
                t('Move to Folder'), t('Move to Folder'), '500', '450')
        );
        if ($fp->canCopyFile()) {
            $this->addItem(new LinkItem('#', t('Duplicate'), [
                'data-file-manager-action' => 'duplicate',
                'data-file-id' => $file->getFileID()
            ]));
        }
        if ($fp->canViewFileInFileManager()) {
            $this->addItem(new DialogLinkItem(
                    \URL::to('/ccm/system/dialogs/file/sets?fID=' . $file->getFileID()),
                    t('Sets'), t('File Sets'), '500', '400')
            );
        }

        if ($fp->canEditFilePermissions() || $fp->canDeleteFile()) {
            $this->addItem(new DividerItem());

        }
        if ($fp->canEditFilePermissions()) {
            $this->addItem(new DialogLinkItem(
                    REL_DIR_FILES_TOOLS_REQUIRED . '/files/permissions?fID=' . $file->getFileID(),
                    t('Permissions'), t('Permissions & Access'), '520', '450')
            );
        }
        $this->addItem(new DialogLinkItem(
                \URL::to('/ccm/system/dialogs/file/usage', $file->getFileID()),
                t('File Usage'), t('File Usage'), '90%', '75%')
        );
        if ($fp->canDeleteFile()) {
            $this->addItem(new DeleteItem($file->getFileNodeObject()));
        }

    }
}
