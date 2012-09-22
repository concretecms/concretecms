<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-social-link-attribute-wrapper">
<? for ($i = 0; $i < count($data['service']); $i++) { ?>
	<div class="ccm-social-link-attribute control-group form-inline">
		<select name="<?=$this->field('service')?>[]" class="ccm-social-link-service-selector">
			<? foreach($services as $s) { ?>
				<? $value = strtolower(str_replace(' ', '', $s[0]));?>
				<option value="<?=$value?>" data-tooltip-title="<?=$s[1]?>" <? if ($value == $data['service'][$i]) { ?> selected="selected" <? } ?>><?=$s[0]?></option>
			<? } ?>
		</select>
		<span class="ccm-social-link-service-text-wrapper">
		<span class="ccm-social-link-service-add-on-wrapper"><span class="add-on"><i></i></span></span><input name="<?=$this->field('serviceInfo')?>[]" type="text" value="<?=$data['serviceInfo'][$i]?>" />
		</span>
		
		<button type="button" class="ccm-social-link-attribute-remove-line btn btn-link"><i class="icon-remove"></i></button>
	</div>
<? } ?>

</div>

<div>
	<button type="button" class="btn btn-small ccm-social-link-attribute-add-service"><?=t("Add Link")?> <i class="icon-plus-sign"></i></button>
</div>