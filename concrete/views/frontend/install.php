<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var string $concreteVersion
 */

?>

<div v-cloak id="ccm-page-install">
    <concrete-installer
            load-strings-url='<?=URL::to('/install/i18n')?>'
            reload-preconditions-url='<?=URL::to('/install')?>'
            validate-environment-url='<?=URL::to('/install/validate_environment')?>'
            :lang='<?= h(json_encode($lang))?>'
            <?php if (isset($locale)) { ?>locale="<?=$locale?>"<?php } ?>
            <?php if (isset($preconditions)) { ?>:preconditions='<?=h(json_encode($preconditions))?>'<?php } ?>
            :locales='<?= h(json_encode($locales))?>'
            :languages='<?= h(json_encode($languages))?>'
            :online-locales='<?= h(json_encode($onlineLocales))?>'
            concrete-version="<?= $concreteVersion ?>"
            site-locale-language="<?=$siteLocaleLanguage?>"
            :countries='<?=h(json_encode($countries))?>'
            site-locale-country="<?=$siteLocaleCountry?>"
            timezone="<?=$timezone?>"
            :timezones='<?=h(json_encode($timezones))?>'
    ></concrete-installer>
</div>

<noscript>
    <div class="text-center lead"><?=t('JavaScript is required to run the Concrete CMS installer. Please enable it in your browser.')?></div>
</noscript>
