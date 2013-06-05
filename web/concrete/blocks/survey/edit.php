<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<style type="text/css">
div.survey-block-option {
	position: relative; border-bottom: 1px solid #ddd; padding-bottom: 3px; padding-top: 3px;
}

div.survey-block-option img {
	position: absolute; top: 3px; right: 0px;
}

</style>
<script type="text/javascript">
	var currentOption = <?=count($controller->options)?>;
</script>
<div class="ccm-ui">

<strong><?=t('Question')?></strong><br/>
<input type="text" style="width: 320px" name="question" value="<?=$controller->getQuestion()?>" />
<br><br>

<strong><?=t('Open to all site visitors?')?></strong><br/>
<input type="radio" value="0" name="requiresRegistration" style="vertical-align: middle" <? if (!$controller->requiresRegistration()) { ?> checked <? } ?> />&nbsp;<?=t('Yes')?>
&nbsp;&nbsp;
<input type="radio" value="1" name="requiresRegistration" style="vertical-align: middle" <? if ($controller->requiresRegistration()) { ?> checked <? } ?> />&nbsp;<?=t('No. Registration is required to answer.')?>
<br><br>

<strong><?=t('Options')?></strong>
<div id="pollOptions">
<? 
$options = $controller->getPollOptions();
if (count($options) == 0) {
	echo t("None");
} else {
	foreach($options as $opt) { ?>		
        <div class="survey-block-option" id="option<?=$opt->getOptionID()?>"><a href="#" onclick="removeOption(<?=$opt->getOptionID()?>); return false"><img src="<?=ASSETS_URL_IMAGES?>/icons/delete_small.png" /></a> <?=$opt->getOptionName()?>
        <input type="hidden" name="survivingOptionNames[]" value="<?=htmlspecialchars($opt->getOptionName())?>" />
        </div>		
	<? }
} ?>
</div>
<br/><br/>
<strong><?=t('Add option')?></strong><br/>

<input type="text" name="optionValue" id="ccm-survey-optionValue" style="width: 320px" />
<input type="button" onclick="addOption()" value="add" class="btn small" />
</div>