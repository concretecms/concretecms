<?php

namespace Concrete\Controller\Backend\File;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class View extends Controller
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/backend/file/view';

    public function view(): ?Response
    {
        $this->set('fileVersion', $this->getFileVersion());
        $this->set('resolverManager', $this->app->make(ResolverManagerInterface::class));
        $this->set('form', $this->app->make(Form::class));

        return null;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getFile(): File
    {
        $fID = $this->getFileID();
        if ($fID === null) {
            throw new UserMessageException(t('Invalid parameters received.'));
        }
        $file = $this->app->make(EntityManagerInterface::class)->find(File::class, $fID);
        if ($file === null) {
            throw new UserMessageException(t('File Not Found.'));
        }
        $this->checkFileAccess($file);

        return $file;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkFileAccess(File $file): void
    {
        $checker = new Checker($file);
        if (!$checker->canViewFileInFileManager()) {
            throw new UserMessageException(t('Access Denied.'));
        }
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getFileVersion(): Version
    {
        $file = $this->getFile();
        $fileVersionID = $this->getFileVersionID();
        if ($fileVersionID === null) {
            $fileVersion = $file->getApprovedVersion();
        } else {
            $fileVersion = $file->getVersion($fileVersionID);
        }
        if ($fileVersion === null) {
            throw new UserMessageException(t('File Version Not Found.'));
        }

        return $fileVersion;
    }

    protected function getFileID(): ?int
    {
        $fID = $this->request->request->get('fID', $this->request->query->get('fID'));

        return $this->app->make(Numbers::class)->integer($fID, 1) ? (int) $fID : null;
    }

    protected function getFileVersionID(): ?int
    {
        $fvID = $this->request->request->get('fvID', $this->request->query->get('fvID'));

        return $this->app->make(Numbers::class)->integer($fvID, 1) ? (int) $fvID : null;
    }
}
