<?php

namespace Concrete\Controller\Dialog\File;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Permission\Checker;
use Doctrine\ORM\EntityManagerInterface;

class Statistics extends Controller
{
    protected $viewPath = '/dialogs/file/statistics';

    public function view($fID)
    {
        $file = $fID ? $this->app->make(EntityManagerInterface::class)->find(File::class, (int) $fID) : null;
        if ($file === null) {
            throw new UserMessageException(t('Unable to find the requested file.'));
        }
        $permissionChecker = new Checker($file);
        if (!$permissionChecker->canViewFileInFileManager()) {
            throw new UserMessageException(t('Access denied to the requested file.'));
        }
        $this->set('file', $file);
    }
}
