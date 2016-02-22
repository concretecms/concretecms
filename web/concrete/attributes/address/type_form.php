<?php
$co = Loader::helper('lists/countries');
$countries = array_merge(array('' => t('Choose Country')), $co->getCountries());

if (isset($_POST['akCustomCountries'])) {
    $akCustomCountries = $_POST['akCustomCountries'];
} elseif (!is_array($akCustomCountries)) {
    $akCustomCountries = array();
}
if (isset($_POST['akHasCustomCountries'])) {
    $akHasCustomCountries = $_POST['akHasCustomCountries'];
}

?>

<fieldset class="ccm-attribute ccm-attribute-address">
<legend><?=t('Address Options')?></legend>

<div class="form-group">
<label><?=t("Available Countries")?></label>
    <div class="radio">
        <label><?=$form->radio('akHasCustomCountries', 0, $akHasCustomCountries)?><?=t('All Available Countries')?></label>
    </div>
    <div class="radio">
        <label><?=$form->radio('akHasCustomCountries', 1, $akHasCustomCountries)?><?=t('Selected Countries')?></label>
    </div>
</div>
<div class="form-group">
	<select id="akCustomCountries" name="akCustomCountries[]" multiple size="7" disabled="disabled" class="form-control">
		<?php foreach ($countries as $key => $val) {
    ?>
			<?php if (empty($key) || empty($val)) {
    continue;
}
    ?>
			<option <?=(in_array($key, $akCustomCountries) || $akHasCustomCountries == 0 ? 'selected ' : '')?>value="<?=$key?>"><?=$val?></option>
		<?php 
} ?>
	</select>
</div>

<div class="form-group">
<label for="akDefaultCountry"><?=t('Default Country')?></label>
<?=$form->select('akDefaultCountry', $countries, $akDefaultCountry, array('classes' => 'form-control'))?>
</fieldset>
