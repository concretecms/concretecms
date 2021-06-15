<?php
namespace Concrete\Core\Calendar\Event\Summary\Template;

use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\Calendar\Summary\CalendarEventTemplate;
use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\Template\AbstractPopulator;

class Populator extends AbstractPopulator
{

    /**
     * @param CalendarEvent $mixed
     */
    public function clearAvailableTemplates(CategoryMemberInterface $mixed)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->delete(CalendarEventTemplate::class, 'ct')
            ->where('ct.event = :event');
        $queryBuilder->setParameter('event', $mixed);
        $queryBuilder->getQuery()->execute();
    }

    public function updateAvailableSummaryTemplates(CategoryMemberInterface $mixed)
    {
        $this->entityManager->refresh($mixed);
        parent::updateAvailableSummaryTemplates($mixed);
    }

    /**
     * @param CalendarEvent $mixed
     */
    public function createCategoryTemplate(CategoryMemberInterface $mixed)
    {
        $eventTemplate = new CalendarEventTemplate();
        $eventTemplate->setEvent($mixed);
        return $eventTemplate;
    }

}
