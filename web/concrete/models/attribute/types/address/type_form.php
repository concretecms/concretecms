<?
$co = Loader::helper('lists/countries');
$countries = array_merge(array('' => t('Choose Country')), $co->getCountries());

if (isset($_POST['akCustomCountries'])) {
	$akCustomCountries = $_POST['akCustomCountries'];
} else if (!is_array($akCustomCountries)) {
	$akCustomCountries = array();
}
if (isset($_POST['akHasCustomCountries'])) {
	$akHasCustomCountries = $_POST['akHasCustomCountries'];
}

?>

<fieldset>
<legend><?=t('Address Options')?></legend>

<div class="clearfix">
<label><?=t("Available Countries")?></label>
<div class="input">
<ul class="inputs-list">
<li><label><?=$form->radio('akHasCustomCountries', 0, $akHasCustomCountries)?> <span><?=t('All Available Countries')?></span></label></li>
<li><label><?=$form->radio('akHasCustomCountries', 1, $akHasCustomCountries)?> <span><?=t('Selected Countries')?></span></label></li>
</ul>
</div>
</div>
<div class="clearfix">
<label></label>
<div class="input">
	<select id="akCustomCountries" name="akCustomCountries[]" multiple size="7" disabled="disabled">
		<? foreach ($countries as $key=>$val) { ?>
			<? if (empty($key) || empty($val)) continue; ?>
			<option <?=(in_array($key, $akCustomCountries) || $akHasCustomCountries == 0 ?'selected ':'')?>value="<?=$key?>"><?=$val?></option>
		<? } ?>
	</select>
</div>
</div>

<div class="clearfix">
<label for="akDefaultCountry"><?=t('Default Country')?></label>
<div class="input">
<?=$form->select('akDefaultCountry', $countries, $akDefaultCountry)?></div>
</div>

</fieldset>