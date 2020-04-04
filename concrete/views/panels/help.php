<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/**
* @var Concrete\Controller\Panel\Help $controller
* @var Concrete\Core\View\DialogView $view
* @var Concrete\Core\User\User $u
* @var bool $showIntro
* @var Concrete\Core\Config\Repository\Repository $config
*/
?>
<div class="ccm-panel-content-inner" id="ccm-panel-help">
    <?php
    if ($showIntro) {
        View::element('help/introduction', compact('config'));
    }
    ?>
</div>
