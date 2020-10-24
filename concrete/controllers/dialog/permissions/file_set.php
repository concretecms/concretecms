<?php

namespace Concrete\Controller\Dialog\Permissions;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\File\Set\Set;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Utility\Service\Validation\Numbers;

defined('C5_EXECUTE') or die('Access Denied.');

class FileSet extends UserInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/dialogs/permissions/file_set';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    public function canAccess()
    {
        $fsp = new Checker($this->getFileSet());

        return $fsp->canEditFileSetPermissions();
    }

    public function view()
    {
        $this->set('fileSet', $this->getFileSet());
    }

    protected function getFileSet(): Set
    {
        $fileSetID = $this->request->request->get('fsID', $this->request->query->get('fsID'));
        if (!$this->app->make(Numbers::class)->integer($fileSetID, 1)) {
            return Set::getGlobal();
        }
        $fileSet = Set::getByID((int) $fileSetID);
        if ($fileSet === null) {
            throw new UserMessageException(t('File Set not found'));
        }

        return $fileSet;
    }
}
