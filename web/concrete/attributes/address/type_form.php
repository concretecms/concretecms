<?php
$co = Core::make('helper/lists/countries');
$countries = array_merge(array('' => t('Choose Country')), $co->getCountries());

if (isset($_POST['akCustomCountries'])) {
	$akCustomCountries = $_POST['akCustomCountries'];
} else if (!is_array($akCustomCountries)) {
	$akCustomCountries = array();
}
if (isset($_POST['akHasCustomCountries'])) {
	$akHasCustomCountries = $_POST['akHasCustomCountries'];
}

if (isset($_POST['akisRequiredAddress1'])) {
	$akisRequiredAddress1 = $_POST['akisRequiredAddress1'];
}
if (isset($_POST['akisRequiredAddress2'])) {
	$akisRequiredAddress2 = $_POST['akisRequiredAddress2'];
}
if (isset($_POST['akisRequiredCity'])) {
	$akisRequiredCity = $_POST['akisRequiredCity'];
}
if (isset($_POST['akisRequiredStateProvince'])) {
	$akisRequiredStateProvince = $_POST['akisRequiredStateProvince'];
}
if (isset($_POST['akisRequiredCountry'])) {
	$akisRequiredCountry = $_POST['akisRequiredCountry'];
}
if (isset($_POST['akisRequiredPostalCode'])) {
	$akisRequiredPostalCode = $_POST['akisRequiredPostalCode'];
}

?>

<fieldset class="ccm-attribute ccm-attribute-address">
<legend><?= t('Address Options')?></legend>
	<div class="form-group">
		<label><?= t("Required fields")?></label>
		<div class="checkbox">
			<label><?= $form->checkbox('akisRequiredAddress1', 1, $akisRequiredAddress1)?><?= t('Address 1')?></label>
		</div>
		<div class="checkbox">
			<label><?= $form->checkbox('akisRequiredAddress2', 1, $akisRequiredAddress2)?><?= t('Address 2')?></label>
		</div>
		<div class="checkbox">
			<label><?= $form->checkbox('akisRequiredCity', 1, $akisRequiredCity)?><?= t('City')?></label>
		</div>
		<div class="checkbox">
			<label><?= $form->checkbox('akisRequiredStateProvince', 1, $akisRequiredStateProvince)?><?= t('State/Province')?></label>
		</div>
		<div class="checkbox">
			<label><?= $form->checkbox('akisRequiredCountry', 1, $akisRequiredCountry)?><?= t('Country')?></label>
		</div>
		<div class="checkbox">
			<label><?= $form->checkbox('akisRequiredPostalCode', 1, $akisRequiredPostalCode)?><?= t('Postal Code')?></label>
		</div>
	</div>
<div class="form-group">
<label><?= t("Available Countries")?></label>
    <div class="radio">
        <label><?= $form->radio('akHasCustomCountries', 0, $akHasCustomCountries)?><?= t('All Available Countries')?></label>
    </div>
    <div class="radio">
        <label><?= $form->radio('akHasCustomCountries', 1, $akHasCustomCountries)?><?= t('Selected Countries')?></label>
    </div>
</div>
<div class="form-group">
	<select id="akCustomCountries" name="akCustomCountries[]" multiple size="7" disabled="disabled" class="form-control">
		<?php foreach ($countries as $key=>$val) { ?>
			<?php if (empty($key) || empty($val)) continue; ?>
			<option <?= (in_array($key, $akCustomCountries) || $akHasCustomCountries == 0 ?'selected ':'')?>value="<?= $key?>"><?= $val?></option>
		<?php } ?>
	</select>
</div>

<div class="form-group">
<label for="akDefaultCountry"><?=t('Default Country')?></label>
<?=$form->select('akDefaultCountry', $countries, $akDefaultCountry, array('classes'=>'form-control'))?>
</fieldset>