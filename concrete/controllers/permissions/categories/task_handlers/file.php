<?php

namespace Concrete\Controller\Permissions\Categories\TaskHandlers;

use Concrete\Core\Entity\File\File as ConcreteFile;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Category\ObjectTaskHandler;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\ObjectInterface;
use Concrete\Core\Workflow\Workflow;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class File extends ObjectTaskHandler
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Category\ObjectTaskHandler::getPermissionObject()
     */
    protected function getPermissionObject(array $options): ObjectInterface
    {
        $fileID = $options['fID'] ?? null;
        $file = $this->app->make(EntityManagerInterface::class)->find(ConcreteFile::class, $fileID);
        if ($file === null) {
            throw new UserMessageException(t('File not received'));
        }
        $fp = new Checker($file);
        if (!$fp->canEditFilePermissions()) {
            throw new UserMessageException(t('Access Denied.'));
        }

        return $file;
    }

    protected function revertToGlobalFilePermissions(ConcreteFile $file, array $options): ?Response
    {
        $file->resetPermissions();

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function overrideGlobalFilePermissions(ConcreteFile $file, array $options): ?Response
    {
        $file->resetPermissions(1);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function savePermissionAssignments(ConcreteFile $file, array $options): ?Response
    {
        $permissions = Key::getList('file');
        foreach ($permissions as $pk) {
            $pk->setPermissionObject($file);
            $pt = $pk->getPermissionAssignmentObject();
            $pt->clearPermissionAssignment();
            $paID = $options['pkID'][$pk->getPermissionKeyID()] ?? 0;
            if ($paID > 0) {
                $pa = Access::getByID($paID, $pk);
                if ($pa !== null) {
                    $pt->assignPermissionAccess($pa);
                }
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function saveWorkflows(ConcreteFile $file, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($file);
        $pk->clearWorkflows();
        foreach (($options['wfID'] ?? []) as $wfID) {
            $wf = Workflow::getByID($wfID);
            if ($wf !== null) {
                $pk->attachWorkflow($wf);
            }
        }

        return null;
    }
}
