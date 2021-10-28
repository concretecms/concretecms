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

?><div class="h-100 pt-4"><?php
    $tabs = array();
    $checked = true;
    foreach ($_REQUEST['cvID'] as $key => $cvID) {
        if (!$val->integer($cvID)) {
            unset($_REQUEST['cvID'][$key]);
        } else {
            $tabs[] = array('ccm-tab-content-view-version-' . $cvID, t('Version %s', $cvID), $checked);
            $checked = false;
        }
    }
    echo $ih->tabs($tabs);

    ?>
    <div class="tab-content h-100">
    <?php
    $i = 0;
    $resolverManager = app(ResolverManagerInterface::class);
    foreach ($_REQUEST['cvID'] as $cvID) {
        ?>
            <div class="tab-pane <?php if ($i == 0) { ?>active<?php } ?> h-100" id="ccm-tab-content-view-version-<?=$cvID?>">
                <iframe border="0" frameborder="0" height="100%" width="100%" src="<?= h($resolverManager->resolve(['/ccm/system/page/preview_version']) . "?cvID={$cvID}&cID={$cID}") ?>"></iframe>
            </div>
        <?php
        $i++;
    }
    ?>
    </div>
</div><?php
