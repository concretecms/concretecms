<?php
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
    foreach ($_REQUEST['cvID'] as $cvID) {
        ?><div id="ccm-tab-content-view-version-<?=$cvID?>" style="display: <?=$display?>; height: 100%">
            <iframe border="0" frameborder="0" height="100%" width="100%" src="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/preview_version?cvID=<?=$cvID?>&amp;cID=<?=$cID?>"></iframe>
        </div>
        <?php
        $display = 'none';
    }
?></div><?php
