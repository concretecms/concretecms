<?php

/* @var Concrete\Core\Application\Application $app */
/* @var Concrete\Core\Entity\Geolocator $geolocator */
/* @var Concrete\Core\Geolocator\GeolocatorController $controller */
/* @var Concrete\Core\Form\Service\Form $form */

$configuration = $geolocator->getGeolocatorConfiguration();
?>
<div class="form-group">
    <?= $form->label('url', t('geoPlugin URL')) ?>
    <?= $form->url('geoplugin-url', $configuration['url'], ['required' => 'required']) ?>
</div>

<div class="form-group">
    <?= $form->label('', t('Fields usage')) ?>
    <div class="checkbox">
    	<label>
            <?= $form->checkbox('geoplugin-trust-city', 1, empty($configuration['skipCity'])) ?>
            <span><?= t('Use the city') ?></span>
        </label>
    </div>
    <div class="checkbox">
        <label>
            <?= $form->checkbox('geoplugin-trust-stateprovince', 1, empty($configuration['skipStateProvince'])) ?>
            <span><?= t('Use the State/Province') ?></span>
        </label>
    </div>
    <div class="checkbox">
        <label>
            <?= $form->checkbox('geoplugin-trust-country', 1, empty($configuration['skipCountry'])) ?>
            <span><?= t('Use the Country') ?></span>
        </label>
    </div>
    <div class="checkbox">
        <label>
            <?= $form->checkbox('geoplugin-trust-latlon', 1, empty($configuration['skipLatitudeLongitude'])) ?>
            <span><?= t('Use the latitude/longitude') ?></span>
        </label>
    </div>
</div>

<div class="text-muted small">
    <?= t('IP Geolocation by <a href="%s" target="_blank">geoPlugin</a>.', 'http://www.geoplugin.com/') ?><br />
    <?= t('geoPlugin is a free service that includes GeoLite data created by MaxMind, available from %s.', '<a href="http://www.maxmind.com" target="_blank">www.maxmind.com</a>') ?>
</div>
