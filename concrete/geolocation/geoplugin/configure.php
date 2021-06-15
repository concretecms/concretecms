<?php
defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Entity\Geolocator $geolocator
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Geolocator\GeolocatorController $controller
 */

$configuration = $geolocator->getGeolocatorConfiguration();
?>
<div class="form-group">
    <?= $form->label('url', t('geoPlugin URL')); ?>
    <?= $form->url('geoplugin-url', $configuration['url'], ['required' => 'required']); ?>
</div>

<div class="form-group">
    <?= $form->label('', t('Fields usage')); ?>
    <div class="form-check">
        <?= $form->checkbox('geoplugin-trust-city', 1, empty($configuration['skipCity'])); ?>
        <?= $form->label('geoplugin-trust-city', t('Use the city'), ['class' => 'form-check-label']); ?>
    </div>

    <div class="form-check">
        <?= $form->checkbox('geoplugin-trust-stateprovince', 1, empty($configuration['skipStateProvince'])); ?>
        <?= $form->label('geoplugin-trust-stateprovince', t('Use the State/Province'), ['class' => 'form-check-label']); ?>
    </div>

    <div class="form-check">
        <?= $form->checkbox('geoplugin-trust-country', 1, empty($configuration['skipCountry'])); ?>
        <?= $form->label('geoplugin-trust-country', t('Use the Country'), ['class' => 'form-check-label']); ?>
    </div>

    <div class="form-check">
        <?= $form->checkbox('geoplugin-trust-latlon', 1, empty($configuration['skipLatitudeLongitude'])); ?>
        <?= $form->label('geoplugin-trust-latlon', t('Use the latitude/longitude'), ['class' => 'form-check-label']); ?>
    </div>
</div>

<div class="text-muted small">
    <?= t('IP Geolocation by <a href="%s" target="_blank">geoPlugin</a>.', 'http://www.geoplugin.com/'); ?><br />
    <?= t('geoPlugin is a free service that includes GeoLite data created by MaxMind, available from %s.', '<a href="http://www.maxmind.com" target="_blank">www.maxmind.com</a>'); ?>
</div>
