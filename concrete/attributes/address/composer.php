<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php $f = Loader::helper('form'); ?>
<?php $co = Loader::helper('lists/countries'); ?>

<div class="ccm-attribute-address-composer-wrapper ccm-attribute-address-<?php echo $key->getAttributeKeyID()?>">

    <div class="form-group">
        <?php echo $f->label($this->field('address1'), t('Address 1'))?>
        <?php echo $f->text($this->field('address1'), $address1)?>
    </div>

    <div class="form-group">
        <?php echo $f->label($this->field('address2'), t('Address 2'))?>
        <?php echo $f->text($this->field('address2'), $address2)?>
    </div>

    <div class="form-group">
        <?php echo $f->label($this->field('city'), t('City'))?>
        <?php echo $f->text($this->field('city'), $city)?>
    </div>

    <div class="form-group ccm-attribute-address-state-province">
        <?php echo $f->label($this->field('state_province'), t('State/Province'))?>
    <?php
    $spreq = $f->getRequestValue($this->field('state_province'));
    if ($spreq != false) {
        $state_province = $spreq;
    }
    $creq = $f->getRequestValue($this->field('country'));
    if ($creq != false) {
        $country = $creq;
    }
    ?>
        <?php echo $f->select($this->field('state_province_select'), array('' => t('Choose State/Province')), $state_province, array('classes'=>'form-control','ccm-attribute-address-field-name' => $this->field('state_province')))?>
        <?php echo $f->text($this->field('state_province_text'), $state_province, array('style' => 'display: none', 'ccm-attribute-address-field-name' => $this->field('state_province')))?>
    </div>

    <?php

    if (!$country && !$search) {
        if ($akDefaultCountry != '') {
            $country = $akDefaultCountry;
        } else {
            $country = 'US';
        }
    }

    $countriesTmp = $co->getCountries();
    $countries = array();
    foreach($countriesTmp as $_key => $_value) {
        if ((!$akHasCustomCountries) || ($akHasCustomCountries && in_array($_key, $akCustomCountries))) {
            $countries[$_key] = $_value;
        }
    }
    $countries = array_merge(array('' => t('Choose Country')), $countries);
    ?>

    <div class="form-group ccm-attribute-address-country">
        <?php echo $f->label($this->field('country'), t('Country'))?>
        <?php echo $f->select($this->field('country'), $countries, $country); ?>
    </div>

    <div class="form-group">
        <?php echo $f->label($this->field('postal_code'), t('Postal Code'))?>
        <?php echo $f->text($this->field('postal_code'), $postal_code)?>
    </div>

</div>

<script type="text/javascript">
//<![CDATA[
$(function() {
	ccm_setupAttributeTypeAddressSetupStateProvinceSelector('ccm-attribute-address-<?php echo $key->getAttributeKeyID()?>');
});
//]]>
</script>