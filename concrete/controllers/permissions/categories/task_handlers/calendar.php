<?php

namespace Concrete\Controller\Permissions\Categories\TaskHandlers;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Entity\Calendar\Calendar as CalendarEntity;
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

class Calendar extends Controller implements TaskHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Category\TaskHandlerInterface::handle()
     */
    public function handle(string $task, array $options): ?Response
    {
        $calendar = $this->getCalendar($options);
        if ($calendar === null) {
            throw new UserMessageException(t('Calendar not received'));
        }
        $method = lcfirst(camelcase($task));
        if (!method_exists($this, $method)) {
            throw new UserMessageException(t('Unknown permission task: %s', $task));
        }

        return $this->{$method}($calendar, $options);
    }

    protected function getCalendar(array $options): ?CalendarEntity
    {
        $calendarID = $options['caID'] ?? null;
        $calendar = $calendarID ? $this->app->make(EntityManagerInterface::class)->find(CalendarEntity::class, $calendarID) : null;
        if ($calendar === null) {
            return null;
        }
        $cp = new Checker($calendar);
        if (!$cp->canEditCalendarPermissions()) {
            throw new UserMessageException(t('Access Denied.'));
        }

        return $calendar;
    }

    protected function addAccessEntity(CalendarEntity $calendar, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($calendar);
        $pa = Access::getByID($options['paID'], $pk);
        $pe = Entity::getByID($options['peID']);
        $pd = empty($options['pdID']) ? null : Duration::getByID($options['pdID']);
        $pa->addListItem($pe, $pd, $options['accessType']);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function removeAccessEntity(CalendarEntity $calendar, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($calendar);
        $pa = Access::getByID($options['paID'], $pk);
        $pe = Entity::getByID($options['peID']);
        $pa->removeListItem($pe);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function savePermission(CalendarEntity $calendar, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($calendar);
        $pa = Access::getByID($options['paID'], $pk);
        $pa->save($options);
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

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function displayAccessCell(CalendarEntity $calendar, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($calendar);
        $this->set('pk', $pk);
        $this->set('pa', Access::getByID($options['paID'], $pk));
        $this->setViewPath('/backend/permissions/labels');

        return null;
    }
}
