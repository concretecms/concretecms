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

$page = Page::getCurrentPage();
$user = UserInfo::getByID($page->getCollectionUserID());
?>

<?php if ($event) { ?>
    <div class="ccm-block-calendar-event-wrapper">
        <?php if ($displayEventName) { ?>
            <div class="ccm-block-calendar-event-header text-center mb-3 mb-md-4">
                <h1 class="ccm-block-page-title">
                    <?php if ($enableLinkToPage && $eventOccurrenceLink) { ?>
                        <a href="<?php echo $eventOccurrenceLink ?>">
                            <?php echo h($event->getName()) ?>
                        </a>
                    <?php } else { ?>
                        <?php echo h($event->getName()) ?>
                    <?php } ?>
                </h1>
                <?php if ($displayEventDescription && $event->getDescription()) { ?>
                    <div class="ccm-block-calendar-event-description text-center">
                        <p class="muted">
                            <?php echo $event->getDescription() ?>
                        </p>
                    </div>
                <?php } ?>

                <?php if ($displayEventDate) { ?>
                    <div class="ccm-block-calendar-event-date-time text-center subtitle-big">
                        <?php echo $formatter->getOccurrenceDateString($occurrence); $page->getCollectionDatePublicObject()->format('F j, Y, '); ?>
                        <?php echo (str_repeat('&nbsp;', 1) . $user->getUserDisplayName()); ?>
                    </div>
                <?php } ?>
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

        <?php if (!empty($event->getAttribute('event_thumbnail'))); { 
            
            $thumbnail = $event->getAttribute('event_thumbnail');
            if ($thumbnail) {
                $thumbnailURL = $thumbnail->getURL();

                ?>
                <div class="ccm-block-event-attribute-thumbnail mb-3 mb-md-5">
                    <img src="<?php echo $thumbnailURL; ?>" class="img-fluid">
                </div>
            <?php } ?>
        <?php } ?>

        <div class="ccm-block-event-details-small mb-3 mb-md-4">
            <div class="ccm-block-event-details-small-event-date my-auto">
                <div class="date-top">13</div>
                <div class="date-bottom">Mar</div>
            </div>
            <div class="d-flex flex-column">
                <div class="pt-0 pt-md-2">
                    <div class="ccm-block-event-details-small-event-title">
                        <h3><?php echo h($event->getName()); ?></h3>
                    </div>
                    <div class="ccm-block-event-details-small-event-location subtitle-big">
                    <i class="fas fa-map-marker-alt me-2"></i>    
                    <?php echo ($event->getAttribute('event_location')); ?>
                        
                    </div>
                </div>
                <div class="ccm-block-event-details-small-event-time mt-auto">
                    <?php echo $formatter->getOccurrenceDateString($occurrence); ?>
                </div>
            </div>
            
        </div>

        <?php if (!empty($event->getAttribute('event_details'))); { ?>
            <div class="ccm-block-event-attribute-event-details">
                <?php echo $event->getAttribute('event_details'); ?>
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
