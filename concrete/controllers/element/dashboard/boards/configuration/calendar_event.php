<?php
namespace Concrete\Controller\Element\Dashboard\Boards\Configuration;

use Concrete\Core\Board\DataSource\DataSourceElementController;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Calendar\Utility\Preferences;
use Concrete\Core\Entity\Board\DataSource\Configuration\CalendarEventConfiguration;
use Concrete\Core\Entity\Board\DataSource\Configuration\PageConfiguration;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Page\Search\Field\Manager as SearchFieldManager;
use Concrete\Controller\Element\Search\SearchFieldSelector;
use Concrete\Core\Search\Field\AttributeKeyField;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

class CalendarEvent extends DataSourceElementController
{

    public function getElement()
    {
        return 'dashboard/boards/configuration/calendar_event';
    }

    public function view()
    {
        $preferences = $this->app->make(Preferences::class);
        $this->set('form', $this->app->make(Form::class));
        $calendars = ['' => t('** No Filtering/Current Site')];
        foreach (Calendar::getList() as $calendar) {
            $calendars[$calendar->getID()] = $calendar->getName();
        }
        $this->set('calendars', $calendars);
        $topicsKey = $preferences->getCalendarTopicsAttributeKey();
        if ($topicsKey) {
            $topicsKeyField = new AttributeKeyField($topicsKey);
        }
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
                if ($topicsKeyField) {
                    $topicsKeyField->setData('treeNodeID', $configuration->getTopicTreeNodeID());
                }
            }
        }
        if ($topicsKeyField) {
            $this->set('topicsKeyField', $topicsKeyField);
        }
    }

}
