<?php
namespace Concrete\Core\Board\DataSource\Saver;

use Concrete\Core\Entity\Board\DataSource\Configuration\CalendarEventConfiguration;
use Concrete\Core\Entity\Board\DataSource\Configuration\Configuration;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Calendar\Calendar;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Calendar\Event\Search\Field\Manager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

defined('C5_EXECUTE') or die("Access Denied.");

class CalendarEventSaver extends AbstractSaver
{

    /**
     * @var Manager
     */
    protected $searchFieldManager;

    public function __construct(Manager $searchFieldManager, EntityManager $entityManager)
    {
        $this->searchFieldManager = $searchFieldManager;
        parent::__construct($entityManager);
    }

    public function createConfiguration(Request $request)
    {
        $calendarEventConfiguration = new CalendarEventConfiguration();

        $fields = $this->searchFieldManager->getFieldsFromRequest($request->request->all());
        $query = new Query();
        $query->setFields($fields);
        $query->setItemsPerPage(0); // has to be here but not used.

        $calendar = $this->entityManager->find(Calendar::class, $request->request->get('calendarID'));
        if ($calendar) {
            $calendarEventConfiguration->setCalendar($calendar);
        }
        $calendarEventConfiguration->setQuery($query);
        $calendarEventConfiguration->setMaxOccurrencesOfSameEvent((int) $request->request->get('maxOccurrencesOfSameEvent'));
        return $calendarEventConfiguration;
    }

    public function createConfigurationFromImport(\SimpleXMLElement $element): Configuration
    {
        $manager = app('manager/search_field/calendar_event');

        $calendarEventConfiguration = new CalendarEventConfiguration();
        $calendarEventConfiguration->setMaxOccurrencesOfSameEvent((int) $element['max-occurrences-of-event']);
        $calendar = $this->entityManager->getRepository(Calendar::class)->findOneByName((string) $element['calendar']);
        if ($calendar) {
            $calendarEventConfiguration->setCalendar($calendar);
        }
        $fields = [];
        if (!empty($element->fields)) {
            foreach ($element->fields->field as $fieldNode) {
                $field = $manager->getFieldByKey((string) $fieldNode['key']);
                $field->loadDataFromImport($fieldNode);
                $fields[] = $field;
            }
        }
        $query = new Query();
        $query->setFields($fields);
        $query->setItemsPerPage(0); // has to be here but not used.
        $calendarEventConfiguration->setQuery($query);
        return $calendarEventConfiguration;
    }


}
