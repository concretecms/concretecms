<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Entity\Calendar\Calendar|null $calendar may be unset in case of errors
 * @var bool $canViewCalendar
 * @var Concrete\Core\Calendar\Event\EventOccurrenceList $list
 * @var int $totalToRetrieve
 * @var int $totalPerPage
 * @var string|null $eventListTitle
 * @var string $titleFormat
 * @var Concrete\Core\Calendar\Event\Formatter\LinkFormatterInterface $linkFormatter
 * @var Concrete\Core\Calendar\Event\Formatter\DateFormatter $formatter
 * @var Concrete\Core\Page\Page|int|null $linkToPage if it's an int, it's 0
 */

if (!isset($calendar) || !$canViewCalendar) {
    return;
}

$pagination = $list->getPagination();
$pagination->setMaxPerPage($totalToRetrieve);
$events = $pagination->getCurrentPageResults();

$service = app('helper/date');
$numEvents = count($events);
?>
<div class="ccm-block-calendar-event-list-wrapper widget-featured-events unbound" data-page="<?= $totalPerPage ?: 3 ?>">
    <?php
    if ($eventListTitle) {
        ?>
        <<?= $titleFormat ?>><?= $eventListTitle ?></<?= $titleFormat ?>>
        <?php
    }
    ?>
    <div class="ccm-block-calendar-event-list" <?= $numEvents > $totalPerPage ? ' style="display:none"' : '' ?>>
        <?php
        if ($events === []) {
            ?>
            <p class="ccm-block-calendar-event-list-no-events"><?= t('There are no upcoming events.') ?></p>
            <?php
        } else {
            foreach ($events as $occurrence) {
                $event = $occurrence->getEvent();
                $description = $event->getDescription();
                $safe_name = strtolower($event->getName());
                $safe_name = preg_replace('/[^a-z ]/i', '', $safe_name);
                $safe_name = str_replace(' ', '+', $safe_name);
                ?>
                <div class="ccm-block-calendar-event-list-event">
                    <div class="ccm-block-calendar-event-list-event-date">
                        <span><?= $service->formatCustom('M', $occurrence->getStart()) ?></span>
                        <span><?= $service->formatCustom('d', $occurrence->getStart()) ?></span>
                    </div>
                    <div class="ccm-block-calendar-event-list-event-title">
                        <?php
                        $url = $linkFormatter->getEventOccurrenceFrontendViewLink($occurrence);
                        if ($url) {
                            ?>
                            <a href="<?= $url ?>"><?= h($event->getName()) ?></a>
                            <?php
                        } else {
                            ?>
                            <?= h($event->getName()) ?>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="ccm-block-calendar-event-list-event-date-full">
                        <?= $formatter->getOccurrenceDateString($occurrence) ?>
                    </div>
                    <?php
                    if ($description) {
                        ?>
                        <div class="ccm-block-calendar-event-list-event-description">
                            <?= $description ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <div class="btn-group ccm-block-calendar-event-list-controls">
        <?php
        if ($numEvents > $totalPerPage) {
            ?>
            <button type="button" class="btn btn-secondary" data-cycle="previous"><i class="fas fa-angle-left"></i></button>
            <button type="button" class="btn btn-secondary" data-cycle="next"><i class="fas fa-angle-right"></i></button>
            <?php
        }
        ?>
    </div>
    <?php
    if ($linkToPage) {
        ?>
        <div class="ccm-block-calendar-event-list-link">
            <a href="<?= $linkToPage->getCollectionLink() ?>"><?= t('View Calendar') ?></a>
        </div>
        <?php
    }
    ?>
</div>
