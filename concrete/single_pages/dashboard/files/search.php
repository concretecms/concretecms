<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

$fp = FilePermissions::getGlobal();

if ($fp->canAddFile() || $fp->canSearchFiles()) {
    ?>
    <div class="ccm-dashboard-content-full">
        <?php View::element('files/search', ['result' => $result]) ?>
    </div>
    <?php
} else {
    ?>
	<p>
        <?= t('You do not have access to the file manager.') ?>
    </p>
    <?php
}
