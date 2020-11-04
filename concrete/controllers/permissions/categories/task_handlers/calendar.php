<?php

namespace Concrete\Controller\Permissions\Categories\TaskHandlers;

use Concrete\Core\Entity\Calendar\Calendar as CalendarEntity;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Permission\Category\ObjectTaskHandler;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\ObjectInterface;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class Calendar extends ObjectTaskHandler
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Category\ObjectTaskHandler::$hasWorkflows
     */
    protected $hasWorkflows = true;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Category\ObjectTaskHandler::getPermissionObject()
     */
    protected function getPermissionObject(array $options): ObjectInterface
    {
        $calendarID = $options['caID'] ?? null;
        $calendar = $calendarID ? $this->app->make(EntityManagerInterface::class)->find(CalendarEntity::class, $calendarID) : null;
        if ($calendar === null) {
            throw new UserMessageException(t('Calendar not received'));
        }
        $cp = new Checker($calendar);
        if (!$cp->canEditCalendarPermissions()) {
            throw new UserMessageException(t('Access Denied.'));
        }

        return $calendar;
    }
}
