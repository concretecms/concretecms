<?php
namespace Concrete\Controller\Element\Dashboard\Boards\Configuration;

use Concrete\Controller\Element\Search\SearchFieldSelector;
use Concrete\Core\Board\DataSource\DataSourceElementController;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Entity\Board\DataSource\Configuration\CalendarEventConfiguration;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Calendar\Event\Search\Field\Manager as SearchFieldManager;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

class CalendarEvent extends DataSourceElementController
{

    public function getElement()
    {
        return 'dashboard/boards/configuration/calendar_event';
    }

    public function view()
    {
        $manager = $this->app->make(SearchFieldManager::class);
        $resolver = $this->app->make(ResolverManagerInterface::class);
        $addFieldAction = "#";
        $fieldSelector = new SearchFieldSelector($manager, $addFieldAction);
        $fieldSelector->setIncludeJavaScript(true);
        $fieldSelector->setAddFieldAction(
            $resolver->resolve(['/ccm/calendar/dialogs/event/advanced_search/add_field'])
        );
        $this->set('form', $this->app->make(Form::class));
        $calendars = ['' => t('** No Filtering/Current Site')];
        foreach (Calendar::getList() as $calendar) {
            $calendars[$calendar->getID()] = $calendar->getName();
        }
        $this->set('calendars', $calendars);
        $this->set('maxOccurrencesOfSameEvent', 0);
        if ($this->configuredDataSource) {
            $configuration = $this->configuredDataSource->getConfiguration();
            if ($configuration) {
                /**
                 * @var $configuration CalendarEventConfiguration
                 */
                $calendar = $configuration->getCalendar();
                if ($calendar) {
                    $this->set('calendarID', $calendar->getID());
                }
                $query = $configuration->getQuery();
                if ($query) {
                    $fieldSelector->setQuery($query);
                }
                $this->set('maxOccurrencesOfSameEvent', $configuration->getMaxOccurrencesOfSameEvent());
            }
        }
        $this->set('fieldSelector', $fieldSelector);
    }

}
