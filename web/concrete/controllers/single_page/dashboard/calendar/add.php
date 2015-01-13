<?php
namespace Concrete\Controller\SinglePage\Dashboard\Calendar;
use Concrete\Core\Calendar\Calendar;
use Core;
use \Concrete\Core\Page\Controller\DashboardPageController;

class Add extends DashboardPageController
{

    public function view($caID = null)
    {
        if ($caID) {
            // we're editing.
            $calendar = Calendar::getByID($caID);
            if (is_object($calendar)) {
                $this->set('calendar', $calendar);
            }
        }
    }

    public function submit() {
        $vs = Core::make('helper/validation/strings');
        $sec = Core::make('helper/security');
        $name = $sec->sanitizeString($this->post('calendarName'));
        if (!$this->token->validate('submit')) {
            $this->error->add(t($this->token->getErrorMessage()));
        }
        if (!$vs->notempty($name)) {
            $this->error->add(t('You must specify a valid name for your calendar.'));
        }
        if ($this->post('caID')) {
            $calendar = Calendar::getByID($this->post('caID'));
            if (!is_object($calendar)) {
                $this->error->add(t('Invalid calendar object.'));
            }
        }
        if (!$this->error->has()) {
            if (!is_object($calendar)) {
                $calendar = new Calendar();
            }
            $calendar->setName($this->request->post('calendarName'));
            $calendar->setColor($this->request->post('caColor'));
            $calendar->save();
            $this->redirect('/dashboard/calendar/events', $calendar->getID());
        }
    }

}