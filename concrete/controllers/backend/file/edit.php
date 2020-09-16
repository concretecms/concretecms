<?php

namespace Concrete\Controller\Backend\File;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class Edit extends Controller
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/backend/file/edit';

    public function view(): ?Response
    {
        $file = $this->getFile();
        $this->checkAccessibleFile($file);
        $this->set('file', $file);
        $this->set('fileVersion', $file->getApprovedVersion());

        return null;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getFile(): File
    {
        $fileID = $this->request->request->get('fID', $this->request->query->get('fID'));
        $fileID = $this->app->make(Numbers::class)->integer($fileID, 1) ? (int) $fileID : null;
        $file = $fileID === null ? null : $this->app->make(EntityManagerInterface::class)->find(File::class, $fileID);
        if ($file === null) {
            throw new UserMessageException(t('Unable to find the specified file.'));
        }

        return $file;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkAccessibleFile(File $file): void
    {
        $fp = new Checker($file);
        if (!$fp->canEditFileContents()) {
            throw new UserMessageException(t('Access Denied.'));
        }
    }
}
