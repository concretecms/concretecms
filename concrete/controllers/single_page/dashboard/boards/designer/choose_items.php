<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards\Designer;

use Concrete\Core\Board\Designer\Command\SetItemSelectorCustomElementItemsCommand;
use Concrete\Core\Board\Instance\Item\Populator\CalendarEventPopulator;
use Concrete\Core\Board\Instance\Item\Populator\PagePopulator;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Calendar\Event\EventOccurrenceService;
use Concrete\Core\Entity\Board\DataSource\DataSource;
use Concrete\Core\Entity\Board\Designer\CustomElement;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;

class ChooseItems extends DashboardSitePageController
{

    public function view($id = null)
    {
        $element = $this->getCustomElement($id);
        if (is_object($element)) {
            $calendars = Calendar::getList();
            $calendarSelect = ['0' => t('** Choose Calendar')];
            foreach($calendars as $calendar) {
                $permissions = new Checker($calendar);
                if ($permissions->canViewCalendarInEditInterface()) {
                    $calendarSelect[$calendar->getID()] = $calendar->getName();
                }
            }
            $this->set('calendarSelect', $calendarSelect);
            $this->set('element', $element);
            $items = [];
            // @TODO - this should be done in a way that's not as hard coded and much more modular. See other comments
            // elsewhere
            $eventOccurrenceService = $this->app->make(EventOccurrenceService::class);
            foreach($element->getItems() as $elementItem) {
                $item = $elementItem->getItem();
                $this->entityManager->refresh($item);
                $dataSourceHandle = $elementItem->getItem()->getDataSource()->getHandle();
                $data = [];
                switch($dataSourceHandle) {
                    case 'calendar_event':
                        $event = $eventOccurrenceService->getByID($elementItem->getItem()->getUniqueItemId());
                        if ($event) {
                            $data['eventVersionOccurrenceId'] = $event->getID();
                            $data['calendarId'] = $event->getEvent()->getCalendar()->getID();
                        }
                        break;
                    case 'page':
                        $page = Page::getByID($elementItem->getItem()->getUniqueItemID());
                        if ($page && !$page->isError()) {
                            $data['pageId'] = $page->getCollectionID();
                        }
                        break;
                }
                $item = [
                    'itemType' => $dataSourceHandle,
                    'data' => $data
                ];
                $items[] = $item;
            }
            $this->set('items', $items);
        } else {
            return $this->redirect('/dashboard/boards/designer');
        }
    }

    /**
     * @param $id
     * @return CustomElement
     */
    protected function getCustomElement($id)
    {
        $r = $this->entityManager->getRepository(CustomElement::class);
        $element = $r->findOneById($id);
        return $element;
    }

    public function submit($elementID = null)
    {
        $element = $this->getCustomElement($elementID);
        if (is_object($element)) {
            $this->set('element', $element);
        }
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $items = [];
        if (!$this->error->has()) {
            // @TODO - This should not be hard coded. We should be able to pick these out of the request
            // but the signature of these drivers isn't set in stone yet, and I don't want to have
            // third party developers build toward it only to have it change. So right now this is hard coded to
            // events and pages.
            $request = $this->request->request->all();
            $pages = [];
            $events = [];

            if (!empty($request['field']['page'])) {
                foreach ((array)$request['field']['page'] as $cID) {
                    $page = Page::getByID($cID);
                    if ($page && !$page->isError()) {
                        $pages[] = $page;
                    }
                }
            }
            $eventOccurrenceService = $this->app->make(EventOccurrenceService::class);
            if (!empty($request['field']['calendar_event'])) {
                foreach ((array)$request['field']['calendar_event'] as $eventOccurrenceID) {
                    $occurrence = $eventOccurrenceService->getByID($eventOccurrenceID);
                    if ($occurrence) {
                        $events[] = $occurrence;
                    }
                }
            }

            $pagePopulator = $this->app->make(PagePopulator::class);
            $calendarEventPopulator = $this->app->make(CalendarEventPopulator::class);
            $pageDataSource = $this->entityManager->getRepository(DataSource::class)->findOneByHandle('page');
            $calendarEventDataSource = $this->entityManager->getRepository(DataSource::class)->findOneByHandle(
                'calendar_event'
            );

            foreach ($pages as $page) {
                $item = $pagePopulator->createItemFromObject($pageDataSource, $page);
                if ($item) {
                    $items[] = $item;
                }
            }
            foreach ($events as $event) {
                $item = $calendarEventPopulator->createItemFromObject($calendarEventDataSource, $event);
                if ($item) {
                    $items[] = $item;
                }
            }
        }
        if (!count($items)) {
            $this->error->add(t('You must choose at least one item for your custom element.'));
        }
        if (!$this->error->has()) {
            // Save the items against the instance.
            $command = new SetItemSelectorCustomElementItemsCommand($element, $items);
            $this->app->executeCommand($command);
            $this->flash('success', t('Designer element items updated.'));
            return $this->buildRedirect(['/dashboard/boards/designer/customize_slot', 'view', $element->getID()]);
        }

        $this->view($elementID);
    }



}
