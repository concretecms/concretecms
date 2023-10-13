<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var string $concreteVersion
 */

?>

<div class="row">
    <div class="col-md-10 offset-md-1 col-xl-8 offset-xl-2">
        <div v-cloak id="ccm-page-install">
            <concrete-installer
                    logo="<?=ASSETS_URL_IMAGES?>/logo_hand_only.svg"
                    load-strings-url='<?=URL::to('/install/i18n')?>'
                    reload-preconditions-url='<?=URL::to('/install')?>'
                    validate-environment-url='<?=URL::to('/install/validate_environment')?>'
                    begin-installation-url='<?=URL::to('/install/begin_installation')?>'
                    :lang='<?= h(json_encode($lang))?>'
                    <?php if (isset($locale)) { ?>locale="<?=$locale?>"<?php } ?>
                    <?php if (isset($preconditions)) { ?>:preconditions='<?=h(json_encode($preconditions))?>'<?php } ?>
                    <?php if (isset($startingPoints)) { ?>:starting-points='<?=h(json_encode($startingPoints))?>'<?php } ?>
                    :locales='<?= h(json_encode($locales))?>'
                    :languages='<?= h(json_encode($languages))?>'
                    :online-locales='<?= h(json_encode($onlineLocales))?>'
                    concrete-version="<?= $concreteVersion ?>"
                    site-locale-language="<?=$siteLocaleLanguage?>"
                    :countries='<?=h(json_encode($countries))?>'
                    site-locale-country="<?=$siteLocaleCountry?>"
                    timezone="<?=$timezone?>"
                    :timezones='<?=h(json_encode($timezones))?>'
                    default-starting-point="atomik"
                    starting-point-routine-url='<?=URL::to('/install/run_routine')?>'
                    installation-complete-url='<?=URL::to('/')?>'
            ></concrete-installer>
        </div>
    </div>
</div>

<noscript>
    <div class="text-center lead"><?=t('JavaScript is required to run the Concrete CMS installer. Please enable it in your browser.')?></div>
</noscript>

