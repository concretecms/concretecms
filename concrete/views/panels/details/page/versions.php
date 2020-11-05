<?php
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

$val = Core::make('helper/validation/numbers');

$cID = 0;
if (isset($_REQUEST['cID']) && $val->integer($_REQUEST['cID'])) {
    $cID = $_REQUEST['cID'];
}
if (!isset($_REQUEST['cvID']) || !is_array($_REQUEST['cvID'])) {
    die(t('Invalid Request.'));
}

?><div style="height: 100%"><?php
    $tabs = array();
    $checked = true;
    foreach ($_REQUEST['cvID'] as $key => $cvID) {
        if (!$val->integer($cvID)) {
            unset($_REQUEST['cvID'][$key]);
        } else {
            $tabs[] = array('view-version-' . $cvID, t('Version %s', $cvID), $checked);
            $checked = false;
        }
    }
    echo $ih->tabs($tabs);

    $display = 'block';
    $resolverManager = app(ResolverManagerInterface::class);
    foreach ($_REQUEST['cvID'] as $cvID) {
        ?><div id="ccm-tab-content-view-version-<?=$cvID?>" style="display: <?=$display?>; height: 100%">
            <iframe border="0" frameborder="0" height="100%" width="100%" src="<?= h($resolverManager->resolve(['/ccm/system/page/preview_version']) . "?cvID={$cvID}&cID={$cID}") ?>"></iframe>
        </div>
        <?php
        $display = 'none';
    }
?></div><?php
