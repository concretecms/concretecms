<?php

namespace Concrete\Core\Permission\Category;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\Entity;
use Concrete\Core\Permission\Duration;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Workflow\Workflow;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Abstrct class for task handles that does not have a permission object.
 */
abstract class GenericTaskHandler extends Controller implements TaskHandlerInterface
{
    /**
     * Should we save workflows when saving permissions?
     *
     * @var bool
     */
    protected $hasWorkflows = false;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Category\TaskHandlerInterface::handle()
     */
    public function handle(string $task, array $options): ?Response
    {
        $this->checkAccess();
        $method = lcfirst(camelcase($task));
        if (!method_exists($this, $method)) {
            throw new UserMessageException(t('Unknown permission task: %s', $task));
        }

        return $this->{$method}($options);
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    abstract protected function checkAccess(): void;

    protected function addAccessEntity(array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pa = Access::getByID($options['paID'], $pk);
        $pe = Entity::getByID($options['peID']);
        $pd = empty($options['pdID']) ? null : Duration::getByID($options['pdID']);
        $pa->addListItem($pe, $pd, $options['accessType']);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function removeAccessEntity(array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pa = Access::getByID($options['paID'], $pk);
        $pe = Entity::getByID($options['peID']);
        $pa->removeListItem($pe);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function savePermission(array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pa = Access::getByID($options['paID'], $pk);
        $pa->save($options);
        if ($this->hasWorkflows) {
            $pa->clearWorkflows();
            $wfIDs = $options['wfID'] ?? null;
            if (is_array($wfIDs)) {
                foreach ($wfIDs as $wfID) {
                    $wf = Workflow::getByID($wfID);
                    if ($wf !== null) {
                        $pa->attachWorkflow($wf);
                    }
                }
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function displayAccessCell(array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $this->set('pk', $pk);
        $this->set('pa', Access::getByID($options['paID'], $pk));
        $this->setViewPath('/backend/permissions/labels');

        return null;
    }
}
