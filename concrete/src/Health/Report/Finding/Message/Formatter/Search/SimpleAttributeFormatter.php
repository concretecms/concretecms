<?php

namespace Concrete\Core\Health\Report\Finding\Message\Formatter\Search;

use Concrete\Core\Entity\Attribute\Value\EventValue;
use Concrete\Core\Entity\Attribute\Value\ExpressValue;
use Concrete\Core\Entity\Attribute\Value\FileValue;
use Concrete\Core\Entity\Attribute\Value\PageValue;
use Concrete\Core\Entity\Attribute\Value\UserValue;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\Health\Report\Finding;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Health\Report\Finding\Control\Location;
use Concrete\Core\Health\Report\Finding\Control\LocationInterface;
use Concrete\Core\Health\Report\Finding\Message\Formatter\FormatterInterface;
use Concrete\Core\Health\Report\Finding\Message\MessageHasDetailsInterface;
use Concrete\Core\Health\Report\Finding\Message\MessageInterface;
use Concrete\Core\Health\Report\Finding\Message\Search\SimpleAttributeMessage;
use Concrete\Core\Page\Page;

class SimpleAttributeFormatter implements FormatterInterface, MessageHasDetailsInterface, HasLocationInterface
{

    /**
     * @param SimpleAttributeMessage $message
     * @return string
     */
    public function getFindingsListMessage(MessageInterface $message, Finding $finding): string
    {
        $value = $message->getCategoryValue();
        $key = $value->getAttributeKey();
        $keyName = $key->getAttributeKeyDisplayName('text');

        if ($value instanceof ExpressValue) {
            $entry = $value->getEntry();
            $entity = $entry->getEntity();
            $message = t(
                '"%s" attribute in %s object "%s" (ID %s)',
                $keyName,
                $entity->getEntityDisplayName('text'),
                $entry->getLabel(),
                $entry->getPublicIdentifier(),
            );
        } else {
            $text = t('Unknown object');
            $objectName = null;
            $objectID = null;
            if ($value instanceof PageValue) {
                $page = Page::getByID($value->getPageID());
                $objectName = $page->getCollectionName();
                $objectID = $value->getPageID();
                $text = 'Page attribute "%s" on page %s (ID %s)';
            }
            if ($value instanceof EventValue) {
                $event = $value->getVersion()->getEvent();
                $objectID = $event->getID();
                $objectName = $event->getName();
                $text = 'Calendar event attribute "%s" on event %s (ID %s)';
            }
            if ($value instanceof FileValue) {
                $version = $value->getVersion();
                $objectID = $version->getFileID();
                $objectName = $version->getFilename();
                $text = 'File attribute "%s" on file %s (ID %s)';
            }
            if ($value instanceof UserValue) {
                $user = $value->getUser();
                $objectID = $user->getUserID();
                $objectName = $user->getUserInfoObject()->getUserDisplayName();
                $text = 'User attribute "%s" on user %s (ID %s)';
            }

            $message = t(
                $text,
                $keyName,
                $objectName,
                $objectID
            );
        }

        return $message;
    }

    public function getDetailsElement(MessageInterface $message, Finding $finding): Element
    {
        return new Element(
            'dashboard/health/report/finding/message/search/details', [
            'details' => $message->getDetails(),
            'result' => $finding->getResult()
        ]
        );
    }

    public function getLocation(MessageInterface $message): ?LocationInterface
    {
        $value = $message->getCategoryValue();
        if ($value instanceof ExpressValue) {
            $entry = $value->getEntry();
            return new Location($entry->getURL(), t('View Entry'));
        } elseif ($value instanceof PageValue) {
            $page = Page::getByID($value->getPageID());
            return new Location($page->getCollectionLink(), t('View Page'));
        } elseif ($value instanceof FileValue) {
            $version = $value->getVersion();
            return new Location(app('url/manager')->resolve(['/dashboard/files/details', $version->getFileID()]), t('View File'));
        } elseif ($value instanceof UserValue) {
            $user = $value->getUser();
            return new Location(app('url/manager')->resolve(['/dashboard/users/search', 'edit', $user->getUserID()]), t('View User'));
        } elseif ($value instanceof EventValue) {
            $event = $value->getVersion()->getEvent();
            /**
             * @var $event CalendarEvent
             */
            return new Location(app('url/manager')->resolve(['/dashboard/calendar/event_list', 'view', $event->getCalendar()->getID()]) . '?eventID=' . $event->getID(),
            t("View Event")
            );
        }
        return null;
    }

}
