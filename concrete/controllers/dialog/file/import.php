<?php

namespace Concrete\Controller\Dialog\File;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\Incoming;
use Concrete\Core\File\Type\TypeList as FileTypeList;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\Utility\Service\Identifier;
use Exception;
use Throwable;

class Import extends UserInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/dialogs/file/import';

    /**
     * The current folder (false: not yet initialized; null: global folder; FileFolder: specific folder).
     *
     * @var \Concrete\Core\Tree\Node\Type\FileFolder|null|false
     */
    private $currentFolder = false;

    /**
     * The permission checker for the current folder.
     *
     * @var \Concrete\Core\Permission\Checker|null
     */
    private $currentFolderPermissions;

    /**
     * The page where the file is originally placed on (false: not yet initialized; null: none; Page: specific page).
     */
    private $originalPage = false;

    public function view()
    {
        $this->setViewHelpers();
        $this->setViewSets();
    }

    /**
     * Set the helpers for the view.
     */
    protected function setViewHelpers()
    {
        $this->set('token', $this->app->make('token'));
        $this->set('form', $this->app->make('helper/form'));
        $this->set('ui', $this->app->make('helper/concrete/ui'));
        $this->set('resolverManager', $this->app->make(ResolverManagerInterface::class));
    }

    /**
     * Set the variables for the view.
     */
    protected function setViewSets()
    {
        $incoming = $this->app->make(Incoming::class);
        $this->set('formID', 'ccm-file-manager-import-files-' . $this->app->make(Identifier::class)->getString(32));
        $this->set('currentFolder', $this->getCurrentFolder());
        $this->set('originalPage', $this->getOriginalPage());
        $this->set('incomingStorageLocation', $incoming->getIncomingStorageLocation());
        $this->set('incomingPath', $incoming->getIncomingPath());
        try {
            $incomingContents = $this->getIncomingFiles();
            $incomingContentsError = null;
        } catch (Exception $e) {
            $incomingContents = [];
            $incomingContentsError = $e->getMessage();
            $incomingContents = $e;
        } catch (Throwable $e) {
            $incomingContents = [];
            $incomingContentsError = $e->getMessage();
        }
        $this->set('incomingContents', $incomingContents);
        $this->set('incomingContentsError', $incomingContentsError);
        $this->set('replacingFile', null);
    }

    /**
     * Get the current folder.
     *
     * @return \Concrete\Core\Tree\Node\Type\FileFolder|null null if global folder, FileFolder instance otherwise
     */
    protected function getCurrentFolder()
    {
        if ($this->currentFolder === false) {
            $currentFolder = null;
            $fID = $this->request->request->get('currentFolder', $this->request->query->get('currentFolder'));
            if ($fID && is_scalar($fID)) {
                $fID = (int) $fID;
                if ($fID !== 0) {
                    $node = Node::getByID($fID);
                    if ($node instanceof FileFolder) {
                        $currentFolder = $node;
                    }
                }
            }
            $this->setCurrentFolder($currentFolder);
        }

        return $this->currentFolder;
    }

    /**
     * Set the current folder.
     *
     * @param \Concrete\Core\Tree\Node\Type\FileFolder|null $value null if global folder, FileFolder instance otherwise
     *
     * @return $this
     */
    protected function setCurrentFolder(FileFolder $value = null)
    {
        if ($value !== $this->currentFolder) {
            $this->currentFolder = $value;
            $this->currentFolderPermissions = null;
        }

        return $this;
    }

    /**
     * Get the permissions for the current folder.
     *
     * @return \Concrete\Core\Permission\Checker
     */
    protected function getCurrentFolderPermissions()
    {
        if ($this->currentFolderPermissions === null) {
            $folder = $this->getCurrentFolder();
            if ($folder === null) {
                $folder = $this->app->make(Filesystem::class)->getRootFolder();
                if ($folder === null) {
                    $folder = new FileFolder();
                }
            }
            $this->currentFolderPermissions = new Checker($folder);
        }

        return $this->currentFolderPermissions;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    protected function canAccess()
    {
        return $this->getCurrentFolderPermissions()->canAddFiles();
    }

    /**
     * Get the page where the file is originally placed on.
     *
     * @return \Concrete\Core\Page\Page|null null: none; Page: specific page
     */
    protected function getOriginalPage()
    {
        if ($this->originalPage === false) {
            $originalPage = null;
            $ocID = $this->request->request->get('ocID', $this->request->query->get('ocID'));
            if ($ocID && is_scalar($ocID)) {
                $ocID = (int) $ocID;
                if ($ocID !== 0) {
                    $originalPage = Page::getByID($ocID);
                }
            }
            $this->setOriginalPage($originalPage);
        }

        return $this->originalPage;
    }

    /**
     * Set the page where the file is originally placed on.
     *
     * @param \Concrete\Core\Page\Page|null $value null: none; Page: specific page
     *
     * @return $this
     */
    protected function setOriginalPage(Page $value = null)
    {
        $this->originalPage = $value === null || $value->isError() ? null : $value;

        return $this;
    }

    /**
     * Get the list of files available in the "incoming" directory.
     *
     * @throws \Exception in case of errors
     *
     * @return string[]
     */
    protected function getIncomingFiles()
    {
        $fh = $this->app->make('helper/validation/file');
        $nh = $this->app->make('helper/number');
        $incoming = $this->app->make(Incoming::class);
        $files = $incoming->getIncomingFilesystem()->listContents($incoming->getIncomingPath());
        foreach (array_keys($files) as $index) {
            $files[$index]['allowed'] = $fh->extension($files[$index]['basename']);
            $files[$index]['thumbnail'] = FileTypeList::getType($files[$index]['extension'])->getThumbnail();
            $files[$index]['displaySize'] = $nh->formatSize($files[$index]['size'], 'KB');
        }

        return $files;
    }
}
