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
<div class="ccm-panel-content-inner" id="ccm-panel-help">
    <?php
    if ($message === null) {
        View::element('help/introduction');
    } else {
        View::element('help/message', compact('message'));
    }
    ?>
    <hr />
    <?php
    View::element('help/more_help', compact('config'));
    ?>
</div>
