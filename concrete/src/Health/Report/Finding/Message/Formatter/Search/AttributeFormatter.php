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
use Concrete\Core\Health\Report\Finding\Message\Search\AttributeMessage;
use Concrete\Core\Page\Page;

class AttributeFormatter implements FormatterInterface, MessageHasDetailsInterface, HasLocationInterface
{

    /**
     * @param AttributeMessage $message
     * @return string
     */
    public function getFindingsListMessage(MessageInterface $findingMessage, Finding $finding): string
    {
        $value = $findingMessage->getCategoryValue();
        if ($value) {
            $key = $value->getAttributeKey();
            $keyName = $key->getAttributeKeyDisplayName('text');

            if ($value instanceof ExpressValue) {
                $entry = $value->getEntry();
                $entity = $entry->getEntity();
                $message = t(
                    /* i18n: %1$s is the name of an attribute key, %2$s is the name of an entity, %3$s is the label of an entity entry, %4$s is the ID of the entry */
                    '"%1$s" attribute in %2$s object "%3$s" (ID %4$s)',
                    $keyName,
                    $entity->getEntityDisplayName('text'),
                    $entry->getLabel(),
                    $entry->getPublicIdentifier(),
                );
            } else {
                $message = t('Unknown object');
                if ($value instanceof PageValue) {
                    $page = Page::getByID($value->getPageID());
                    $message = t(
                    /* i18n: %1$s is the name of an attribute key, %2$s is the name of a page, %3$s is the ID of the page */
                        'Page attribute "%1$s" on page %2$s (ID %3$s)',
                        $keyName,
                        $page->getCollectionName(),
                        $value->getPageID()
                    );
                }
                if ($value instanceof EventValue) {
                    $event = $value->getVersion()->getEvent();
                    $message = t(
                    /* i18n: %1$s is the name of an attribute key, %2$s is the name of an event, %3$s is the ID of the event */
                        'Calendar event attribute "%1$s" on event %2$s (ID %3$s)',
                        $keyName,
                        $event->getName(),
                        $event->getID()
                    );
                }
                if ($value instanceof FileValue) {
                    $version = $value->getVersion();
                    $message = t(
                    /* i18n: %1$s is the name of an attribute key, %2$s is the name of a file, %3$s is the ID of the file */
                        'File attribute "%1$s" on file %2$s (ID %3$s)',
                        $keyName,
                        $version->getFilename(),
                        $version->getFileID()
                    );
                }
                if ($value instanceof UserValue) {
                    $user = $value->getUser();
                    $message = t(
                    /* i18n: %1$s is the name of an attribute key, %2$s is a username, %3$s is the ID of the user */
                        'User attribute "%1$s" on user %2$s (ID %3$s)',
                        $keyName,
                        $user->getUserInfoObject()->getUserDisplayName(),
                        $user->getUserID()
                    );
                }
            }
        } else {
            $message = t('Unknown attribute. Perhaps this has already been deleted.');
        }

        return $message;
    }

    public function getDetailsElement(MessageInterface $message, Finding $finding): Element
    {
        return new Element(
            'dashboard/health/report/finding/message/search/details', [
            'details' => $this->getDetailsString($message),
            'result' => $finding->getResult()
        ]
        );
    }

    public function getDetailsString(MessageInterface $message): string
    {
        return $message->getDetails();
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
