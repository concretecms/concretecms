<?php 
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
	<td class="subheader"><?php echo t("Available Countries")?></td>
</tr>
<tr>
	<td style="padding-right: 15px">
		<div><?php echo $form->radio('akHasCustomCountries', 0, $akHasCustomCountries)?> <?php echo $form->label('akHasCustomCountries1', t('All Available Countries'))?></div>
		<div><?php echo $form->radio('akHasCustomCountries', 1, $akHasCustomCountries)?> <?php echo $form->label('akHasCustomCountries2', t('Selected Countries'))?></div>
		
		<select id="akCustomCountries" name="akCustomCountries[]" multiple size="7" style="width:100%" disabled>
			<?php  foreach ($countries as $key=>$val) { ?>
				<?php  if (empty($key) || empty($val)) continue; ?>
				<option <?php echo (in_array($key, $akCustomCountries) || $akHasCustomCountries == 0 ?'selected ':'')?>value="<?php echo $key?>"><?php echo $val?></option>
			<?php  } ?>
		</select>
	</td>
	
</tr>

<tr>
	<td class="subheader"><?php echo t('Default Country')?></td>
</tr>
<tr>
	<td><?php echo $form->select('akDefaultCountry', $countries, $akDefaultCountry)?></td>
</tr>

</table>