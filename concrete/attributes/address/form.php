<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Attribute\Address\Controller $controller */
/* @var Concrete\Core\Attribute\View $view */
/* @var Concrete\Core\Attribute\View $this */

/* @var Concrete\Core\Entity\Attribute\Key\UserKey $key */

/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Core\Localization\Service\CountryList $lists_countries */

/* @var string $address1 */
/* @var string $address2 */
/* @var string $city */
/* @var string $state_province */
/* @var string $country */
/* @var string $postal_code */

/* @var int $akID */
/* @var array $akCustomCountries */
/* @var string $akDefaultCountry */
/* @var bool $akGeolocateCountry */
/* @var bool $akHasCustomCountries */
/* @var bool $search */
?>

<div class="ccm-attribute-address-composer-wrapper ccm-attribute-address-<?= $key->getAttributeKeyID() ?>">

    <div class="form-group ccm-attribute-address-line">
        <?= $form->label($this->field('address1'), t('Address 1')) ?>
        <?= $form->text($this->field('address1'), $address1) ?>
    </div>

    <div class="form-group ccm-attribute-address-line">
        <?= $form->label($this->field('address2'), t('Address 2')) ?>
        <?= $form->text($this->field('address2'), $address2) ?>
    </div>

    <div class="form-group ccm-attribute-address-line">
        <?= $form->label($this->field('city'), t('City')) ?>
        <?= $form->text($this->field('city'), $city) ?>
    </div>

    <div class="form-group ccm-attribute-address-line ccm-attribute-address-state-province">
        <?= $form->label($this->field('state_province'), t('State/Province')) ?>
        <?= $form->text($this->field('state_province'), $state_province, ['data-countryfield' => $this->field('country')]) ?>
    </div>

    <?php
    if (!$country && !$search) {
        if ($akDefaultCountry != '') {
            $country = $akDefaultCountry;
        }
    }
    ?>

    <div class="form-group ccm-attribute-address-line ccm-attribute-address-country">
        <?= $form->label($this->field('country'), t('Country')) ?>
        <?= $form->selectCountry($this->field('country'), $country, [
            'allowedCountries' => $akHasCustomCountries ? $akCustomCountries : null,
            'linkStateProvinceField' => true,
        ]) ?>
    </div>

    <div class="form-group ccm-attribute-address-line">
        <?= $form->label($this->field('postal_code'), t('Postal Code')) ?>
        <?= $form->text($this->field('postal_code'), $postal_code) ?>
    </div>

</div>
