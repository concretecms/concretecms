<?php
namespace Concrete\Controller\SinglePage\Dashboard\Events;

use Concrete\Core\Calendar\Event\Event;
use Concrete\Core\Calendar\Event\EventList;
use Concrete\Core\Calendar\Event\EventRepetition;
use Concrete\Core\Foundation\Repetition\RepetitionInterface;
use Concrete\Core\Page\Controller\DashboardPageController;
use Symfony\Component\HttpFoundation\JsonResponse;

class Events extends DashboardPageController
{

    public function delete($id = null, $token = null)
    {
        $this->view();

        if (!$id || !$token) {
            $this->error->add(t('Invalid delete request, please try again.'));
            return;
        }

        if (!\Core::make('helper/validation/token')->validate('delete_event', $token)) {
            $this->error->add(t('Invalid delete token, please try again.'));
            return;
        }

        if ($event = Event::getByID($id)) {
            if ($event->delete()) {
                $this->redirect('dashboard', 'events', 'events', 'delete_success');
            }
        }

    }

    public function view()
    {
        $event_list = new EventList();
        $event_list->setupAutomaticSorting();
        $this->set('event_list', $event_list);
    }

    public function delete_success()
    {
        $this->view();
        $this->set('message', t('Event successfully deleted!'));
    }

    public function duration_overlay()
    {
        $request = \Request::getInstance();
        $result = array('error' => null, 'result' => null);

        $event = Event::getByID($request->get('id'));

        ob_start();
        \Loader::element('events/repetition', array('event' => $event));
        $result['result'] = ob_get_contents();
        ob_end_clean();

        $response = new JsonResponse($result, $result['error'] ? $result['error']['code'] : 200);
        $response->send();

        \Core::shutdown();
        exit;
    }

    protected function loadRepetition(RepetitionInterface $rep, $array)
    {
        $rep->setStartDate(date('Y-m-d H:i:s', strtotime(array_get($array, 'startDate'))));
        $rep->setEndDate(date('Y-m-d H:i:s', strtotime(array_get($array, 'endDate'))));

        $rep->setStartDateAllDay(!!array_get($array, 'startDateAllDay'));
        $rep->setEndDateAllDay(!!array_get($array, 'endDateAllDay'));

        $rep->setRepeatEveryNum(intval(array_get($array, 'repeatEveryNum', 1), 10));
        $rep->setRepeatPeriod(intval(array_get($array, 'repeatPeriod'), 10));
        $rep->setRepeatPeriodEnd(array_get($array, 'repeatPeriodEnd'));
        $rep->setRepeatMonthBy(intval(array_get($array, 'repeatMonthBy'), 10));

        $week_days = array_filter(
            array_map(
                function ($int) {
                    $intval = intval($int, 10);
                    if ($intval >= 0 && $intval < 7) {
                        return $intval;
                    }
                    return null;
                },
                array_get($array, 'repeatPeriodWeekDays', array())),
            function ($int) {
                return $int !== null;
            });
        $rep->setRepeatPeriodWeekDays($week_days);

        return $rep;
    }

    public function update_repetition()
    {
        $this->view();
        $result = array('error' => null, 'result' => null);
        $response = new JsonResponse();

        $request = \Request::getInstance();
        $id = $request->post('id', 0);
        $token = $request->post('token', '');

        if (\Core::make('helper/validation/token')->validate('update_repetition', $token)) {

            if ($event = Event::getByID($id)) {
                $repetition_string = (string)\Request::getInstance()->post('repetition', '{}');
                $array = json_decode($repetition_string, 1);

                $rep = $event->getRepetition();
                $this->loadRepetition($rep, $array);

                $rep->save();
            } else {
                $result['error'] = t('Invalid event, please try again.');
            }
        } else {

            $result['error'] = t('Invalid token, please try again.');
        }

        $response->setData($result);
        $response->send();
        \Core::shutdown();
    }

    public function add_overlay()
    {
        $result = array('error' => null, 'result' => null);

        ob_start();
        \Loader::element('events/add');
        $result['result'] = ob_get_contents();
        ob_end_clean();

        $response = new JsonResponse($result, $result['error'] ? $result['error']['code'] : 200);
        $response->send();

        \Core::shutdown();
        exit;
    }

    public function add_event()
    {
        $this->view();
        $result = array('error' => null, 'result' => null);
        $response = new JsonResponse();
        $request = \Request::getInstance();

        $token = $request->post('token', '');

        if (!\Core::make('helper/validation/token')->validate('add_event', $token)) {
            $result['error'] = t('Invalid token, please try again.');
            $response->setData($result);
            $response->send();
            \Core::shutdown();
        }

        $event_string = (string)\Request::getInstance()->post('event', '{}');
        $event_object = (array)@json_decode($event_string, true);

        if (array_get($event_object, 'name', $this) === $this ||
            array_get($event_object, 'description', $this) === $this ||
            array_get($event_object, 'repetition', $this) === $this
        ) {
            $result['error'] = t('Invalid request, please try again.');
            $response->setData($result);
            $response->send();
            \Core::shutdown();
        }

        $repetition = new EventRepetition();
        $repetition->setStartDate(date('Y-m-d'));
        $repetition->setStartDateAllDay(true);
        $repetition->setEndDate(date('Y-m-d'));
        $repetition->setEndDateAllDay(true);
        $this->loadRepetition($repetition, array_get($event_object, 'repetition'));
        $repetition->save();

        $event = new Event(
            array_get($event_object, 'name', ''),
            array_get($event_object, 'description', ''),
            $repetition);
        $event->save();

        $response->setData($result);
        $response->send();
        \Core::shutdown();
    }

    public function add_success()
    {
        $this->set('message', 'Event added successfully!');
    }

}
