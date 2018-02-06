<?php
namespace Concrete\Controller\SinglePage\Dashboard\Calendar;

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Page\Controller\DashboardCalendarPageController;
use Concrete\Core\Entity\Calendar\Calendar as CalendarEntity;
use Concrete\Core\Calendar\Calendar;
use Core;
use Concrete\Core\Calendar\Utility\Preferences;

class Add extends DashboardCalendarPageController
{
    public function view($caID = null)
    {
        if ($caID) {
            // we're editing.
            $calendar = Calendar::getByID($caID);
            $cp = new \Permissions($calendar);
            if (!$cp->canEditCalendar()) {
                unset($calendar);
            }
        }

        if (isset($calendar)) {
            $this->set('calendar', $calendar);
        } elseif ($caID) {
            throw new \Exception(t('Access Denied.'));
        }

        $attributeKeys = array('' => t('** Choose a Calendar Event Attribute'));
        $types = array('' => t('** Choose a Page Type'));
        $list = Type::getList(false, $this->site->getType());
        foreach ($list as $pt) {
            $types[$pt->getPageTypeID()] = $pt->getPageTypeDisplayName();
        }
        $keys = CollectionKey::getList();
        foreach ($keys as $ak) {
            if ($ak->getAttributeTypeHandle() == 'calendar_event') {
                $attributeKeys[$ak->getAttributeKeyHandle()] = $ak->getAttributeKeyDisplayName();
            }
        }
        $this->set('attributeKeys', $attributeKeys);
        $this->set("types", $types);
    }

    public function submit()
    {
        $vs = Core::make('helper/validation/strings');
        $sec = Core::make('helper/security');
        $name = $sec->sanitizeString($this->post('calendarName'));
        if (!$this->token->validate('submit')) {
            $this->error->add(t($this->token->getErrorMessage()));
        }

        $calendar = null;
        if (!$vs->notempty($name)) {
            $this->error->add(t('You must specify a valid name for your calendar.'));
        }
        if ($this->post('caID')) {
            $calendar = Calendar::getByID($this->post('caID'));
            $cp = new \Permissions($calendar);
            if (!$cp->canEditCalendar()) {
                $this->error->add(t("You do not have permission to edit this calendar."));
            }
        } else {
            $permissions = new \Permissions();
            if (!$permissions->canAddCalendar()) {
                $this->error->add(t("You do not have permission to add a calendar."));
            }
        }

        if ($this->request->request->has('enableMoreDetails') && $this->request->request->get('enableMoreDetails') == 'create') {
            $type = Type::getByID($this->request->request->get('eventPageTypeID'));
            if (!is_object($type)) {
                $this->error->add(t('You must choose a page type for the calendar event page.'));
            }

            $ak = CollectionKey::getByHandle($this->request->request->get('eventPageAttributeKeyHandle'));
            if (!is_object($ak)) {
                $this->error->add(t('You must choose a valid calendar event attribute to store the event within.'));
            }

            $parent = null;
            if ($this->request->request->has('eventPageParentID')) {
                $parent = Page::getByID($this->request->request->get('eventPageParentID'));
                if (!is_object($parent) || $parent->isError()) {
                    unset($parent);
                }
            }

            if (!$parent) {
                $this->error->add(t('You must choose a parent page for the calendar event pages.'));
            }
        }

        if (!$this->error->has()) {
            if (!is_object($calendar)) {
                $calendar = new CalendarEntity();
            }
            $calendar->setSite($this->getSite());
            switch($this->request->request->get('enableMoreDetails')) {
                case 'associate':
                    $calendar->setEnableMoreDetails('A');
                    $calendar->setEventPageParentID(0);
                    $calendar->setEventPageTypeID(0);
                    $calendar->setEventPageAttributeKeyHandle(null);
                    $calendar->setEventPageAssociatedID($this->request->request->get('eventPageAssociatedID'));
                    break;

                case 'create':
                    $calendar->setEnableMoreDetails('C');
                    $calendar->setEventPageParentID($this->request->request->get('eventPageParentID'));
                    $calendar->setEventPageTypeID($this->request->request->get('eventPageTypeID'));
                    $calendar->setEventPageAttributeKeyHandle($this->request->request->get('eventPageAttributeKeyHandle'));
                    $calendar->setEventPageAssociatedID(0);
                    break;
                default:
                    $calendar->setEventPageAssociatedID(0);
                    $calendar->setEnableMoreDetails(null);
                    $calendar->setEventPageParentID(0);
                    $calendar->setEventPageTypeID(0);
                    $calendar->setEventPageAttributeKeyHandle(null);
                    // Do nothing
                    break;
            }

            $calendar->setName($this->request->post('calendarName'));
            $calendar = Calendar::save($calendar);

            $preferences = $this->app->make(Preferences::class);

            $this->redirect($preferences->getPreferredViewPath(), $calendar->getID());
        }
        $this->view();
    }
}
