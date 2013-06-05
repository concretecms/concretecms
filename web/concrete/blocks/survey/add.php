<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
<style type="text/css">
div.survey-block-option {
	position: relative; border-bottom: 1px solid #ddd; padding-bottom: 3px; padding-top: 3px;
}

div.survey-block-option img {
	position: absolute; top: 3px; right: 0px;
}

</style>

<script>
var currentOption = 0;
</script>
<div class="ccm-ui">
<strong><?=t('Question')?></strong><br/>
<input type="text" style="width: 320px" name="question" value="" />
<br><br>
<strong><?=t('Open to all site visitors?')?></strong><br/>
<input type="radio" value="0" name="requiresRegistration" style="vertical-align: middle" checked />&nbsp;<?=t('Yes')?>
&nbsp;&nbsp;
<input type="radio" value="1" name="requiresRegistration" style="vertical-align: middle" />&nbsp;<?=t('No. Registration is required to answer.')?>
<br><br>
<strong><?=t('Options')?></strong>
<div id="pollOptions">
<?=t('None')?>
</div>

<br/><br/>
<strong><?=t('Add option')?></strong><br/>
<input type="text" name="optionValue" id="ccm-survey-optionValue" style="width: 320px" />
<input type="button" onclick="addOption()" value="<?=t('Add')?>" class="btn small" />
</div>