<?php
namespace Concrete\Controller\Dialog\Event;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
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
        $this->eventService = $app->make(EventService::class);
    }

    protected function canAccess()
    {
        $event = $this->eventService->getByID($_REQUEST['eventID'], EventService::EVENT_VERSION_RECENT);
        if (is_object($event)) {
            $calendar = $event->getCalendar();
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
        $event = $this->eventService->getByID($_REQUEST['eventID'], EventService::EVENT_VERSION_RECENT);
        if (!$event) {
            throw new \Exception(t('Invalid event.'));
        }
        $pageTemplates = $event->getSummaryTemplates();
        $selectedTemplateIDs = [];
        $templates = [];
        $selectedTemplates = $event->getCustomSelectedSummaryTemplates();
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
        $this->set('memberIdentifier', $event->getID());
        $this->set('object', $event);
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
                $templateIDs = $this->request->request->get('templateIDs');
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
