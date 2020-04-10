<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/**
* @var Concrete\Controller\Panel\Help $controller
* @var Concrete\Core\View\DialogView $view
* @var Concrete\Core\User\User $u
* @var Concrete\Core\Config\Repository\Repository $config
* @var Concrete\Core\Application\Service\UserInterface\Help\MessageInterface|null $message
*/
?>
<?php
if ($message === null) {
    View::element('help/introduction');
} else {
    View::element('help/message', compact('message'));
}
?>
<hr />
<?php
View::element('help/resources', compact('config'));
?>

<script>
$(document).ready(function() {
    $('#ccm-panel-help a[data-lightbox=iframe]').magnificPopup({
        disableOn: 700,
        type: 'iframe',
        mainClass: 'mfp-fade',
        removalDelay: 160,
        preloader: false,
        fixedContentPos: false
    });
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
