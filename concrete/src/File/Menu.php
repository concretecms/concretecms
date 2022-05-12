<?php

namespace Concrete\Core\File;

use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\DialogLinkItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\DividerItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\File\Menu\Item\DeleteFileItem;
use Concrete\Core\Tree\Menu\Item\DeleteItem;

class Menu extends DropdownMenu
{

    protected $menuAttributes = ['class' => 'ccm-popover-file-menu'];

    public function __construct(FileEntity $file)
    {
        parent::__construct();

        $this->setAttribute('data-search-file-menu', $file->getFileID());


        $fp = new \Permissions($file);
        if ($fp->canViewFile()) {

            if ($file->canView()) {
                $this->addItem(new DialogLinkItem(
                        \URL::to('/ccm/system/file/view?fID=' . $file->getFileID()),
                        t('View'), t('View'), '90%', '75%')
                );
            }

            $this->addItem(new LinkItem('#', t('Download'), [
                'data-file-manager-action' => 'download',
                'data-file-id' => $file->getFileID()
            ]));

        }

        $this->addItem(new DividerItem());

        if ($file->canEdit() && $fp->canEditFileContents()) {
            $this->addItem(new DialogLinkItem(
                    \URL::to('/ccm/system/file/edit?fID=' . $file->getFileID()),
                    t('Edit'), t('Edit'), '90%', '75%')
            );
        }

        if ($fp->canViewFileInFileManager()) {
            $this->addItem(new LinkItem(
                \URL::to('/dashboard/files/details', $file->getFileID()),
                    t('Details')
            ));
        }

        if ($fp->canEditFileProperties()) {
            $this->addItem(
                new DialogLinkItem(
                    \URL::to('/ccm/system/dialogs/file/folder?fID=' . $file->getFileID()),
                    t('Move to Folder'), t('Move to Folder'), '500', '450'
                )
            );
        }

        if ($fp->canCopyFile()) {
            $this->addItem(new LinkItem('#', t('Duplicate'), [
                'data-file-manager-action' => 'duplicate',
                'data-file-id' => $file->getFileID()
            ]));
        }
        if ($fp->canDeleteFile()) {
            $this->addItem(new DividerItem());
            $this->addItem(new DeleteFileItem($file));
        }
    }
}
