<?php

namespace Concrete\Controller\Dialog\Permissions;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Entity\File\File as ConcreteFile;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class File extends UserInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/dialogs/permissions/file';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    public function canAccess()
    {
        $fp = new Checker($this->getFile());

        return $fp->canEditFilePermissions();
    }

    public function view()
    {
        $this->set('file', $this->getFile());
    }

    protected function getFile(): ConcreteFile
    {
        $fileID = $this->request->request->get('fID', $this->request->query->get('fID'));
        $fileID = $this->app->make(Numbers::class)->integer($fileID, 1) ? (int) $fileID : null;
        $file = $fileID === null ? null : $this->app->make(EntityManagerInterface::class)->find(ConcreteFile::class, $fileID);
        if ($file === null) {
            throw new UserMessageException(t('File not found'));
        }

        return $file;
    }
}
