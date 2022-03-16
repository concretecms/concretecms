<?php

use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/** @var \Concrete\Core\Application\Service\UserInterface $ih */
/** @var int $cID */
/** @var int[] $versions */

if (count($versions) > 1) {
    $newVersionID = $versions[array_key_first($versions)];
    $compareVersionID = $versions[array_key_last($versions)];
}
?>
    <div class="h-100 pt-4">
    <?php
    $tabs = [];
    $checked = true;
    if (isset($newVersionID) && isset($compareVersionID)) {
        $tabs[] = ['ccm-tab-content-compare-versions', t('Changes between version %d and %d', $newVersionID, $compareVersionID), true];
        $checked = false;
    }
    foreach ($versions as $cvID) {
        $tabs[] = ['ccm-tab-content-view-version-' . $cvID, t('Version %s', $cvID), $checked];
        $checked = false;
    }
    echo $ih->tabs($tabs);

    ?>
        <div class="tab-content h-100">
            <?php
            $active = true;
            /** @var ResolverManagerInterface $resolverManager */
            $resolverManager = app(ResolverManagerInterface::class);

            if (isset($newVersionID) && isset($compareVersionID)) {
                $url = $resolverManager->resolve(['/ccm/system/page/preview_version'])
                    ->setQuery(['cID' => $cID, 'cvID' => $newVersionID, 'compareVersionID' => $compareVersionID]);
                ?>
                <div class="tab-pane active h-100" id="ccm-tab-content-compare-versions">
                    <iframe border="0" frameborder="0" height="100%" width="100%"
                            src="<?= h($url) ?>"></iframe>
                </div>
                <?php
                $active = false;
            }

            foreach ($versions as $cvID) {
                $url = $resolverManager->resolve(['/ccm/system/page/preview_version'])
                    ->setQuery(['cID' => $cID, 'cvID' => $cvID]);
                ?>
                <div class="tab-pane <?php if ($active) { ?>active<?php } ?> h-100"
                     id="ccm-tab-content-view-version-<?= $cvID ?>">
                    <iframe border="0" frameborder="0" height="100%" width="100%"
                            src="<?= h($url) ?>"></iframe>
                </div>
                <?php
                $active = false;
            }
            ?>
        </div>
    </div><?php
