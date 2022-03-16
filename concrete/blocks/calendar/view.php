<?php defined('C5_EXECUTE') or die('Access Denied.');

/** @var \Concrete\Block\Calendar\Controller $controller */
/** @var \Concrete\Core\Permission\Checker $permissions */

/** @var string $viewTypeString */
/** @var string $defaultView */
/** @var \Concrete\Core\Block\View\BlockView $view */

/** @var int $bID */
$bID = $bID ?? 0;
$eventLimit = $eventLimit ?? 0;
$navLinks = $navLinks ?? 0;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Page\Page;

if (!isset($calendar) || !is_object($calendar)) {
    $calendar = null;
}
$c = Page::getCurrentPage();
if ($c->isEditMode()) {
    $loc = Localization::getInstance();
    $loc->pushActiveContext(Localization::CONTEXT_UI);
    ?><div class="ccm-edit-mode-disabled-item"><?=t('Calendar disabled in edit mode.')?></div><?php
    $loc->popActiveContext();
    /** @phpstan-ignore-next-line */
} elseif ($calendar !== null && $permissions->canViewCalendar()) { ?>
    <div class="ccm-block-calendar-wrapper" data-calendar="<?=$bID?>"></div>

    <script>
        $(function() {
            $('div[data-calendar=<?=$bID?>]').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: '<?= $viewTypeString ?: ''; ?>'
                },
                locale: <?= json_encode(Localization::activeLanguage()); ?>,
                views: {
                    listDay: { buttonText: '<?= t('list day'); ?>' },
                    listWeek: { buttonText: '<?= t('list week'); ?>' },
                    listMonth: { buttonText: '<?= t('list month'); ?>' },
                    listYear: { buttonText: '<?= t('list year'); ?>' }
                },
                contentHeight: 'auto',

                <?php if ($defaultView) { ?>
                    defaultView: '<?= $defaultView; ?>',
                <?php } ?>
                <?php if ($navLinks) { ?>
                    navLinks: true,
                <?php } ?>
                <?php if ($eventLimit) { ?>
                    eventLimit: true,
                <?php } ?>

                events: '<?=$view->action('get_events')?>',
                nextDayThreshold: '00:00:00',
                eventDataTransform: function(event) {
                    if(event.allDay) {
                        event.end = moment(event.end).add(1, 'days')
                    }
                    return event;
                },

                eventRender: function(event, element) {
                    <?php if ($controller->supportsLightbox()) { ?>
                        element.attr('href', '<?=rtrim(\Concrete\Core\Support\Facade\Url::route(['/view_event', 'calendar'], $bID))?>/' + event.id).magnificPopup({
                            type: 'ajax',
                            callbacks: {
                                beforeOpen: function () {
                                    // just a hack that adds mfp-anim class to markup
                                    this.st.mainClass = 'mfp-zoom';
                                }
                            },
                            closeBtnInside: true,
                            closeOnContentClick: true,
                            midClick: true // allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source.
                        });
                    <?php } ?>
                }
            });
        });
    </script>
<?php
} ?>
