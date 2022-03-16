<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Attribute\Key\EventKey;
use Concrete\Core\Support\Facade\Url;

/** @var Concrete\Core\Calendar\Event\Formatter\DateFormatter $formatter */
/** @var Concrete\Core\Entity\Calendar\CalendarEventVersion|null $event */
/** @var Concrete\Core\Entity\Calendar\CalendarEventVersionOccurrence $occurrence */
/** @var string $mode */
/** @var string $eventOccurrenceLink */
/** @var string $calendarEventAttributeKeyHandle */
/** @var int $calendarID */
/** @var int $eventID */
/** @var string $displayEventAttributes */
/** @var bool $enableLinkToPage */
/** @var bool $displayEventName */
/** @var bool $displayEventDate */
/** @var bool $displayEventDescription */
/** @var array $calendarEventPageKeys */
/** @var array $calendars */
/** @var array $displayEventAttributes */
/** @var bool $allowExport */

?>

<?php if ($event) { ?>
    <div class="ccm-block-calendar-event-wrapper">
        <?php if ($displayEventName) { ?>
            <div class="ccm-block-calendar-event-header">
                <h3>
                    <?php if ($enableLinkToPage && $eventOccurrenceLink) { ?>
                        <a href="<?php echo $eventOccurrenceLink ?>">
                            <?php echo h($event->getName()) ?>
                        </a>
                    <?php } else { ?>
                        <?php echo h($event->getName()) ?>
                    <?php } ?>
                </h3>
            </div>
        <?php } ?>

        <?php if ($displayEventDate) { ?>
            <div class="ccm-block-calendar-event-date-time">
                <?php echo $formatter->getOccurrenceDateString($occurrence) ?>
            </div>
        <?php } ?>

        <?php if ($displayEventDescription && $event->getDescription()) { ?>
            <div class="ccm-block-calendar-event-description">
                <p>
                    <?php echo $event->getDescription() ?>
                </p>
            </div>
        <?php } ?>

        <?php if (count($displayEventAttributes)) { ?>
            <div class="ccm-block-calendar-event-attributes">
                <?php foreach ($displayEventAttributes as $akID) {
                    $ak = EventKey::getByID($akID);

                    if (is_object($ak)) {
                        echo $event->getAttribute($ak->getAttributeKeyHandle(), 'displaySanitized');
                    }
                }
                ?>
            </div>
        <?php } ?>

        <?php if ($allowExport) { ?>
            <div class="ccm-block-calendar-event-export">
                <a href="<?php echo Url::to('/ccm/calendar/event/export')->setQuery(['eventID' => $event->getID()]); ?>"
                   title="<?php echo h(t('Export Event')); ?>" class="btn btn-secondary">
                    <?php echo t('Export Event'); ?>
                </a>
            </div>
        <?php } ?>
    </div>
<?php } ?>
