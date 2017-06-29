<?php defined('C5_EXECUTE') or die('Access Denied.');
$ih = Core::make('multilingual/interface/flag');
?>
<div class="ccm-block-switch-language-flags">
    <div class="ccm-block-switch-language-flags-label"><?= $label ?></div>
    <?php
    foreach ($languageSections as $ml) {
        ?>
        <a href="<?= $controller->resolve_language_url($cID, $ml->getCollectionID()) ?>" title="<?= $ml->getLanguageText($locale) ?>" class="<?php if ($activeLanguage == $ml->getCollectionID()) { ?>ccm-block-switch-language-active-flag<?php } ?>"><?= $ih->getSectionFlagIcon($ml) ?></a>
        <?php
    }
    ?>
</div>