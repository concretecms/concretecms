<?php  defined('C5_EXECUTE') or die("Access Denied."); ?> 
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

<strong><?php echo t('Question')?></strong><br/>
<input type="text" style="width: 350px" name="question" value="" />
<br><br>
<strong><?php echo t('Open to all site visitors?')?></strong><br/>
<input type="radio" value="0" name="requiresRegistration" style="vertical-align: middle" checked />&nbsp;<?php echo t('Yes')?>
&nbsp;&nbsp;
<input type="radio" value="1" name="requiresRegistration" style="vertical-align: middle" />&nbsp;<?php echo t('No. Registration is required to answer.')?>
<br><br>
<strong><?php echo t('Options')?></strong>
<div id="pollOptions">
<?php echo t('None')?>
</div>

<br/><br/>
<strong><?php echo t('Add option')?></strong><br/>
<input type="text" name="optionValue" id="ccm-survey-optionValue" style="width: 350px" />
<input type="button" onclick="addOption()" value="<?php echo t('Add')?>" />