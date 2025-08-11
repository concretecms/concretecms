<?php
defined('C5_EXECUTE') or die('Access Denied.');

/** @var \Concrete\Core\Form\Service\Form $form */
/** @var \Concrete\Core\Application\Service\FileManager $fileManager */
/** @var \Concrete\Core\Editor\EditorInterface $editor */

$brandingText = $brandingText ?? '';
if ($includeBrandText && $includeBrandLogo) {
    $brandingMode = 'logoText';
} else if ($includeBrandLogo) {
    $brandingMode = 'logo';
} else {
    $brandingMode = 'text';
}
$multilingualEnabled = $multilingualEnabled ?? false;
?>

<div data-view="edit-top-navigation-bar-block">
    <fieldset class="mb-3">
        <div class="mb-3">
            <legend><?=t('Options')?></legend>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="includeNavigation" name="includeNavigation" value="1" v-model="includeNavigation">
                <label class="form-check-label" for="includeNavigation"><?=t('Show pages in the navigation bar.')?></label>
            </div>
            <div class="form-check form-switch" v-if="includeNavigation">
                <input type="checkbox" class="form-check-input" id="includeNavigationDropdowns" name="includeNavigationDropdowns" value="1" v-model="includeNavigationDropdowns">
                <label class="form-check-label" for="includeNavigationDropdowns"><?=t('Include child pages in dropdown menus.')?></label>
            </div>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="includeTransparency" name="includeTransparency" value="1" v-model="includeTransparency">
                <label class="form-check-label" for="includeTransparency"><?=t('Enable transparent functionality if the theme supports it.')?></label>
            </div>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="includeStickyNav" name="includeStickyNav" value="1" v-model="includeStickyNav">
                <label class="form-check-label" for="includeStickyNav"><?=t('Enable sticky navigation bar on scroll if theme supports it.')?></label>
            </div>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="includeSearchInput" name="includeSearchInput" value="1" v-model="includeSearchInput">
                <label class="form-check-label" for="includeSearchInput"><?=t('Display search input within navigation bar.')?></label>
            </div>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="ignorePermissions" name="ignorePermissions" value="1" v-model="ignorePermissions">
                <label class="form-check-label" for="ignorePermissions"><?=t('Ignore page permissions.')?></label>
            </div>
            <?php if ($multilingualEnabled) { ?>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="includeSwitchLanguage" name="includeSwitchLanguage" value="1" v-model="includeSwitchLanguage">
                <label class="form-check-label" for="includeSwitchLanguage"><?=t('Display switch language within navigation bar')?></label>
            </div>
            <?php } ?>
        </div>
    </fieldset>
    <fieldset class="mb-3 border-top pt-3">
        <legend><?=t('Branding')?></legend>
        <div class="mb-3">
            <div class="form-check">
                <input type="radio" class="form-check-input" id="brandingModeText" name="brandingMode" value="text" v-model="brandingMode">
                <label class="form-check-label" for="brandingModeText"><?=t('Include Text Branding.')?></label>
            </div>
            <div class="form-check">
                <input type="radio" class="form-check-input" id="brandingModeLogo" name="brandingMode" value="logo" v-model="brandingMode">
                <label class="form-check-label" for="brandingModeLogo"><?=t('Include Logo.')?></label>
            </div>
            <div class="form-check">
                <input type="radio" class="form-check-input" id="brandingModeLogoText" name="brandingMode" value="logoText" v-model="brandingMode">
                <label class="form-check-label" for="brandingModeLogoText"><?=t('Include Text and Logo.')?></label>
            </div>
        </div>
        <div class="mb-3" v-if="brandingMode == 'logoText' || brandingMode == 'text'">
            <label class="form-label" for="logo"><?=t('Text Branding')?></label>
            <input type="text" name="brandingText" class="form-control" value="<?= h($brandingText) ?>">
            <div class="help-block"><?=t('Leave blank to inherit this text from the global site name.')?></div>
        </div>
        <div class="mb-3" v-if="brandingMode == 'logoText' || brandingMode == 'logo'">
            <label class="form-label" for="brandingLogo"><?=t('Logo')?></label>
            <concrete-file-input choose-text="<?=t('Choose Logo')?>" input-name="brandingLogo" file-id="<?=$brandingLogo ?? null ?>"></concrete-file-input>
        </div>
        <div class="mb-3" v-if="includeTransparency && (brandingMode == 'logoText' || brandingMode == 'logo')">
            <label class="form-label" for="brandingTransparentLogo"><?=t('Transparent Logo')?></label>
            <concrete-file-input choose-text="<?=t('Choose Logo')?>" input-name="brandingTransparentLogo" file-id="<?=$brandingTransparentLogo ?? null ?>"></concrete-file-input>
        </div>
    </fieldset>
    <fieldset v-if="includeSearchInput" class="border-top pt-3">
        <legend><?=t('Search')?></legend>
        <div class="mb-3">
            <label class="form-label" for="searchInputFormActionPageID"><?=t('Search Results Page')?></label>
            <concrete-page-input choose-text="<?=t('Choose Page')?>" page-id="<?=$searchInputFormActionPageID ?? null ?>" input-name="searchInputFormActionPageID"></concrete-page-input>
        </div>
    </fieldset>
</div>

<script type="text/javascript">

    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: 'div[data-view=edit-top-navigation-bar-block]',
            components: config.components,
            data: {
                includeTransparency: <?=$includeTransparency ? 'true' : 'false'?>,
                includeNavigation: <?=$includeNavigation ? 'true' : 'false'?>,
                includeNavigationDropdowns: <?=$includeNavigationDropdowns ? 'true' : 'false'?>,
                includeStickyNav: <?=$includeStickyNav ? 'true' : 'false'?>,
                includeSearchInput: <?=$includeSearchInput ? 'true' : 'false'?>,
                includeSwitchLanguage: <?=$includeSwitchLanguage ? 'true' : 'false'?>,
                brandingLogo: <?=(int) ($brandingLogo ?? null)?>,
                brandingTransparentLogo: <?=(int) ($brandingTransparentLogo ?? null)?>,
                searchInputFormActionPageID: <?=(int) ($searchInputFormActionPageID ?? null)?>,
                brandingMode: '<?=$brandingMode?>',
                ignorePermissions: <?=$ignorePermissions ? 'true' : 'false'?>,
            }
        })
    })


</script>
