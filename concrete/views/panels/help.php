<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/**
* @var Concrete\Controller\Panel\Help $controller
* @var Concrete\Core\View\DialogView $view
* @var Concrete\Core\User\User $u
* @var Concrete\Core\Config\Repository\Repository $config
* @var Concrete\Core\Page\Page|null $page
* @var Concrete\Core\Application\Service\UserInterface\Help\Message|null $message
* @var Concrete\Core\Application\Service\UserInterface\Help\MessageFormatterInterface $messageFormatter
*/
?>

<?php if (!$page->isSystemPage()) { ?>
    <div class="ccm-panel-close"><a href="#"><button type="button" class="btn-close"></button></a></div>
<?php } ?>

<?php
if ($message === null) {
    if ($page === null || !$page->isSystemPage()) {
        echo '<div class="mb-5">';
        View::element('help/introduction');
        echo '</div>';
    }
} else {
    echo '<div class="mb-5">';
    View::element('help/message', compact('message', 'messageFormatter'));
    echo '</div>';
}
View::element('help/resources', compact('config'));
?>

<script>
$(document).ready(function() {
    if ($.fn.magnificPopup) {
        $('#ccm-panel-help a[data-lightbox=iframe]').magnificPopup({
            disableOn: 700,
            type: 'iframe',
            mainClass: 'mfp-fade',
            removalDelay: 160,
            preloader: false,
            fixedContentPos: false
        });
    }
    $('#ccm-panel-help a[data-launch-guide]').on('click', function(e) {
        e.preventDefault();
        var guide = $(this).data('launch-guide'),
            tour = ConcreteHelpGuideManager.getGuide(guide);
        if (tour === undefined) {
            console.error('Guide "' + guide + '" is not defined');
            return;
        }
        tour.start();
    });
});
</script>
