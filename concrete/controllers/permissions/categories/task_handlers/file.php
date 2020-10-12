<?php

namespace Concrete\Controller\Permissions\Categories\TaskHandlers;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Entity\File\File as ConcreteFile;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\Entity;
use Concrete\Core\Permission\Category\TaskHandlerInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Duration;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Workflow\Workflow;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class File extends Controller implements TaskHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Category\TaskHandlerInterface::handle()
     */
    public function handle(string $task, array $options): ?Response
    {
        $file = $this->getFile($options);
        if ($file === null) {
            throw new UserMessageException(t('File not received'));
        }
        $method = lcfirst(camelcase($task));
        if (!method_exists($this, $method)) {
            throw new UserMessageException(t('Unknown permission task: %s', $task));
        }

        return $this->{$method}($file, $options);
    }

    protected function getFile(array $options): ?ConcreteFile
    {
        $fileID = empty($options['fID']) ? 0 : (int) $options['fID'];
        if ($fileID === 0) {
            return null;
        }
        $file = $this->app->make(EntityManagerInterface::class)->find(ConcreteFile::class, $fileID);
        if ($file === null) {
            return null;
        }
        $fp = new Checker($file);
        if (!$fp->canEditFilePermissions()) {
            throw new UserMessageException(t('Access Denied.'));
        }

        return $file;
    }

    protected function addAccessEntity(ConcreteFile $file, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($file);
        $pa = Access::getByID($options['paID'], $pk);
        $pe = Entity::getByID($options['peID']);
        $pd = empty($options['pdID']) ? null : Duration::getByID($options['pdID']);
        $pa->addListItem($pe, $pd, $options['accessType']);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
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

    protected function removeAccessEntity(ConcreteFile $file, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($file);
        $pa = Access::getByID($options['paID'], $pk);
        $pe = Entity::getByID($options['peID']);
        $pa->removeListItem($pe);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function savePermission(ConcreteFile $file, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($file);
        $pa = Access::getByID($options['paID'], $pk);
        $pa->save($options);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function displayAccessCell(ConcreteFile $file, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($file);
        $this->set('pk', $pk);
        $this->set('pa', Access::getByID($options['paID'], $pk));
        $this->setViewPath('/backend/permissions/labels');

        return null;
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
    }
}
