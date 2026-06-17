<?php

namespace Concrete\Core\Board\Instance\Notifier;

use Concrete\Core\Entity\Board\DataSource\Configuration\CalendarEventConfiguration;

defined('C5_EXECUTE') or die("Access Denied.");

class CalendarEventNotifier extends AbstractNotifier
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Board\Instance\Notifier\NotifierInterface::findBoardInstancesThatMayContainObject()
     *
     * @param \Concrete\Core\Entity\Calendar\CalendarEvent $object
     */
    public function findBoardInstancesThatMayContainObject($object): array
    {
        $site = $object->getCalendar()->getSite();
        $instances = $this->filterBySite($site);
        $instances = $this->filterByHasConfiguration($instances, CalendarEventConfiguration::class);

        return $instances;
    }
}
