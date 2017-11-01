<?php
defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getCurrentPage();
if ($c->isEditMode()) {
    ?>
    <div class="ccm-edit-mode-disabled-item"><?=t('Calendar disabled in edit mode.')?></div>
    <?php

} elseif (is_object($calendar) && $permissions->canViewCalendar()) {
    ?>

    <div class="ccm-block-calendar-wrapper" data-calendar="<?=$bID?>">


    </div>

    <script type="text/javascript">
        $(function() {
            $('div[data-calendar=<?=$bID?>]').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,basicWeek,basicDay'
                },
                lang: <?= json_encode(Localization::activeLanguage());
    ?>,

                events: '<?=$view->action('get_events')?>',

                eventRender: function(event, element) {
                    <?php if ($controller->supportsLightbox()) {
    ?>
                        element.attr('href', '<?=rtrim(URL::route(array('/view_event', 'calendar'), $bID))?>/' + event.id).magnificPopup({
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
                    <?php

}
    ?>
                }
            });

        });
    </script>

<?php
} ?>
