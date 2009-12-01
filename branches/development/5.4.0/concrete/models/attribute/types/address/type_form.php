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
<table class="entry-form" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader"><?=t("Available Countries")?></td>
</tr>
<tr>
	<td style="padding-right: 15px">
		<div><?=$form->radio('akHasCustomCountries', 0, $akHasCustomCountries)?> <?=$form->label('akHasCustomCountries1', t('All Available Countries'))?></div>
		<div><?=$form->radio('akHasCustomCountries', 1, $akHasCustomCountries)?> <?=$form->label('akHasCustomCountries2', t('Selected Countries'))?></div>
		
		<select id="akCustomCountries" name="akCustomCountries[]" multiple size="7" style="width:100%" disabled>
			<? foreach ($countries as $key=>$val) { ?>
				<? if (empty($key) || empty($val)) continue; ?>
				<option <?=(in_array($key, $akCustomCountries) || $akHasCustomCountries == 0 ?'selected ':'')?>value="<?=$key?>"><?=$val?></option>
			<? } ?>
		</select>
	</td>
	
</tr>

<tr>
	<td class="subheader"><?=t('Default Country')?></td>
</tr>
<tr>
	<td><?=$form->select('akDefaultCountry', $countries, $akDefaultCountry)?></td>
</tr>

</table>