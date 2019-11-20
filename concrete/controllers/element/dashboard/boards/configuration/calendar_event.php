<?php
namespace Concrete\Controller\Element\Dashboard\Boards\Configuration;

use Concrete\Core\Board\DataSource\DataSourceElementController;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Entity\Board\DataSource\Configuration\CalendarEventConfiguration;
use Concrete\Core\Entity\Board\DataSource\Configuration\PageConfiguration;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Page\Search\Field\Manager as SearchFieldManager;
use Concrete\Controller\Element\Search\SearchFieldSelector;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

class CalendarEvent extends DataSourceElementController
{
    
    public function getElement()
    {
        return 'dashboard/boards/configuration/calendar_event';
    }

    public function view()
    {
        $this->set('form', $this->app->make(Form::class));
        $calendars = ['' => t('** Choose a Calendar')];
        foreach (Calendar::getList() as $calendar) {
            $calendars[$calendar->getID()] = $calendar->getName();
        }
        $this->set('calendars', $calendars);
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
            }
        }
    }

}
