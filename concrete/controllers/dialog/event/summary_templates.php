<?php
namespace Concrete\Controller\Dialog\Event;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Calendar\Event\EventOccurrenceService;
use Concrete\Core\Calendar\Event\EventService;
use Concrete\Core\Calendar\Event\Summary\Template\Command\DisableCustomCalendarEventSummaryTemplatesCommand;
use Concrete\Core\Calendar\Event\Summary\Template\Command\EnableCustomCalendarEventSummaryTemplatesCommand;
use Concrete\Core\Calendar\Event\EditResponse;
use Concrete\Core\Support\Facade\Facade;

class SummaryTemplates extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/summary_templates/choose';

    /**
     * @var EventService
     */
    protected $eventService;

    public function __construct()
    {
        parent::__construct();
        $app = Facade::getFacadeApplication();
        $this->eventOccurrenceService = $app->make(EventOccurrenceService::class);
    }

    protected function canAccess()
    {
        $occurrence = $this->eventOccurrenceService->getByID($_REQUEST['versionOccurrenceID']);
        if (is_object($occurrence)) {
            $calendar = $occurrence->getEvent()->getCalendar();
            if (is_object($calendar)) {
                $cp = new \Permissions($calendar);
                return $cp->canEditCalendarEvents();
            }
        }

        return false;
    }

    public function action()
    {
        $url = call_user_func_array('parent::action', func_get_args());
        $url .= '&eventID=' . $_REQUEST['eventID'];

        return $url;
    }


    public function view()
    {
        $occurrence = $this->eventOccurrenceService->getByID($this->request->query->get('versionOccurrenceID'));
        if (!$occurrence) {
            throw new \Exception(t('Invalid occurrence.'));
        }
        $pageTemplates = $occurrence->getSummaryTemplates();
        $selectedTemplateIDs = [];
        $templates = [];
        $selectedTemplates = $occurrence->getCustomSelectedSummaryTemplates();
        if ($selectedTemplates) {
            foreach($selectedTemplates as $selectedTemplate) {
                $selectedTemplateIDs[] = $selectedTemplate->getID();
            }
        }
        if ($pageTemplates) {
            foreach($pageTemplates as $pageTemplate) {
                $templates[] = $pageTemplate;
            }
        }
        $this->set('categoryHandle', 'calendar_event');
        $this->set('memberIdentifier', $occurrence->getID());
        $this->set('object', $occurrence);
        $this->set('templates', $templates);
        $this->set('selectedTemplateIDs', $selectedTemplateIDs);
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $event = $this->eventService->getByID($_REQUEST['eventID'], EventService::EVENT_VERSION_RECENT);
            if (!$event) {
                throw new \Exception(t('Invalid event.'));
            }
            if ($this->request->request->get('hasCustomSummaryTemplates')) {
                $command = new EnableCustomCalendarEventSummaryTemplatesCommand($event->getID());
                $keys = array_keys($this->request->request->all());
                foreach($keys as $key) {
                    if (substr($key, 0, 8) === 'template') {
                        $templateIDs[] = substr($key, 9);
                    }
                }
                if ($templateIDs) {
                    $command->setTemplateIDs($templateIDs);
                }
            } else {
                $command = new DisableCustomCalendarEventSummaryTemplatesCommand($event->getID());
            }
            $this->app->executeCommand($command);

            $r = new EditResponse();
            $r->setTitle(t('Event Updated'));
            $r->setMessage(t('Summary templates settings saved.'));
            $r->outputJSON();
        }
    }
}
