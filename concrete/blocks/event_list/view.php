<?php

defined('C5_EXECUTE') or die('Access Denied.');

/** @var \Concrete\Core\Entity\Calendar\Calendar|null $calendar */
/** @var int $totalToRetrieve */
/** @var int $totalPerPage */
/** @var Concrete\Core\Calendar\Event\EventOccurrenceList $list */
/** @var bool $canViewCalendar */
/** @var Concrete\Core\Calendar\Event\Formatter\LinkFormatter $linkFormatter */
/** @var Concrete\Core\Calendar\Event\Formatter\DateFormatter $formatter */
/** @var \Concrete\Core\Page\Page|null $linkToPage */

if (isset($calendar)) {
    $pagination = $list->getPagination();
    $pagination->setMaxPerPage($totalToRetrieve);
    $events = $pagination->getCurrentPageResults();

    if ($canViewCalendar) {
        /** @var Concrete\Core\Localization\Service\Date $service */
        $service = app('helper/date');
        $c = \Concrete\Core\Page\Page::getCurrentPage();
        $cID = $c->getCollectionID();
        $numEvents = count($events);
        ?>
    <div class="ccm-block-calendar-event-list-wrapper widget-featured-events unbound" data-page="<?= $totalPerPage ?: 3 ?>">
    <?php if (!empty($eventListTitle)) {
    ?>
        <<?php echo $titleFormat ?? 'h5'; ?>><?=$eventListTitle?></<?php echo $titleFormat ?? 'h5'; ?>>

    <?php

}
        ?>
        <div class="ccm-block-calendar-event-list" <?php if ($numEvents > $totalPerPage) {
    echo 'style="display:none"';
}
        ?>>

    <?php
    $total = count($events);
    if ($total) {
        foreach ($events as $occurrence) {
            $event = $occurrence->getEvent();
            $description = $event->getDescription();
            $safe_name = strtolower($event->getName());
            $safe_name = preg_replace('/[^a-z ]/i', '', $safe_name);
            $safe_name = str_replace(' ', '+', $safe_name);
            ?>
            <div class="ccm-block-calendar-event-list-event">
                <div class="ccm-block-calendar-event-list-event-date">
                    <span><?=$service->formatCustom('M', $occurrence->getStart())?></span>
                    <span><?=$service->formatCustom('d', $occurrence->getStart())?></span>
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
                    <?php
                        echo $formatter->getOccurrenceDateString($occurrence);
                    ?>
                </div>
                <?php if ($description) {
    ?>
                    <div class="ccm-block-calendar-event-list-event-description">
                        <?=$description?>
                    </div>
                <?php

}
            ?>
            </div>

        <?php

        }
        ?>

    <?php

    } else {
        ?>
        <p class="ccm-block-calendar-event-list-no-events"><?=t('There are no upcoming events.')?></p>
    <?php

    }
        ?>

        </div>

        <div class="btn-group ccm-block-calendar-event-list-controls">
            <?php if ($numEvents > $totalPerPage) {
    ?>
            <button type="button" class="btn btn-default" data-cycle="previous"><i class="fas fa-angle-left"></i></button>
                <button type="button" class="btn btn-default" data-cycle="next"><i class="fas fa-angle-right"></i></button>
            <?php

}
        ?>
        </div>

        <?php if ($linkToPage) { ?>
            <div class="ccm-block-calendar-event-list-link">
                <a href="<?=$linkToPage->getCollectionLink()?>"><?=t('View Calendar')?></a>
            </div>
        <?php } ?>

    </div>
    <?php

    }
    ?>

<?php
} ?>
