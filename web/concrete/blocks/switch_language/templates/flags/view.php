<?php defined('C5_EXECUTE') or die("Access Denied.");
$ih = Core::make('multilingual/interface/flag');
?>
<div class="ccm-block-switch-language-flags">
	<div class="ccm-block-switch-language-flags-label"><?php echo $label?></div>
    <?php foreach($languageSections as $ml) { ?>
        <a href="<?=$view->action('switch_language', $cID, $ml->getCollectionID())?>"
           title="<?=$ml->getLanguageText($locale)?>"
        class="<? if ($activeLanguage == $ml->getCollectionID()) { ?>ccm-block-switch-language-active-flag<? } ?>"><?
            print $ih->getSectionFlagIcon($ml);
        ?></a>
    <? } ?>
</div>