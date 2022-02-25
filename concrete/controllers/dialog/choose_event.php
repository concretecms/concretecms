<?php
namespace Concrete\Controller\Dialog;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Calendar\CalendarServiceProvider;
use Concrete\Core\Calendar\Event\EventOccurrenceList;
use Concrete\Core\Permission\Checker;
use Symfony\Component\HttpFoundation\JsonResponse;

class ChooseEvent extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/event/choose';

    /**
     * @return bool
     */
    protected function canAccess()
    {
        $calendar = Calendar::getByID($_REQUEST['caID']);
        if (is_object($calendar)) {
            $cp = new Checker($calendar);

            return $cp->canViewCalendarInEditInterface();
        }

        return false;
    }

    /**
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Punic\Exception
     * @throws \Punic\Exception\BadArgumentType
     */
    public function getEvents()
    {
        $calendar = Calendar::getByID($this->request->query->get('caID',0));
        $start = $this->request->query->get('start');
        $end = $this->request->query->get('end');
        $list = new EventOccurrenceList();
        $list->filterByCalendar($calendar);
        $list->filterByStartTimeAfter(strtotime($start));
        $list->filterByStartTimeBefore(strtotime($end));
        $results = $list->getResults();
        $data = [];
        $service = new Date();
        $formatter = $this->app->make(CalendarServiceProvider::class)
            ->getLinkFormatter();
        foreach ($results as $occurrence) {
            $event = $occurrence->getEvent();
            $background = $formatter->getEventOccurrenceBackgroundColor($occurrence);
            $text = $formatter->getEventOccurrenceTextColor($occurrence);
            $obj = new \stdClass();
            $obj->id = $event->getID();
            $obj->occurrenceID = $occurrence->getID();
            $obj->title = $event->getName();
            $repetition = $occurrence->getRepetition();
            if ($repetition->isStartDateAllDay()) {
                $obj->allDay = true;
                $obj->start = $service->formatCustom('Y-m-d', $occurrence->getStart());
                $obj->end = $service->formatCustom('Y-m-d', $occurrence->getEnd());
            } else {
                $obj->start = $service->formatCustom('Y-m-d H:i:s', $occurrence->getStart());
                $obj->end = $service->formatCustom('Y-m-d H:i:s', $occurrence->getEnd());
            }
            $obj->backgroundColor = $background;
            $obj->borderColor = $background;
            $obj->textColor = $text;
            $data[] = $obj;
        }

        $js = new JsonResponse($data);

        return $js;
    }

    /**
     * @return void
     */
    public function view()
    {
        $this->requireAsset('fullcalendar');
        $this->set('calendar', Calendar::getByID($this->request->query->get('caID', 0)));
    }
}
