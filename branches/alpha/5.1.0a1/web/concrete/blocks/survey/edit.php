<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
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
<strong>Question</strong><br/>
<input type="text" style="width: 350px" name="question" value="<?=$controller->getQuestion()?>" />
<br><br>

<strong>Open to all site visitors?</strong><br/>
<input type="radio" value="0" name="requiresRegistration" style="vertical-align: middle" <? if (!$controller->requiresRegistration()) { ?> checked <? } ?> />&nbsp;Yes
&nbsp;&nbsp;
<input type="radio" value="1" name="requiresRegistration" style="vertical-align: middle" <? if ($controller->requiresRegistration()) { ?> checked <? } ?> />&nbsp;No (requires registration to answer)
<br><br>

<strong>Options</strong>
<div id="pollOptions">
<? 

$options = $controller->getPollOptions();
if (count($options) == 0) {
	echo "None";
} else {
	foreach($options as $opt) { ?>
		
        <div class="survey-block-option" id="option<?=$opt->getOptionID()?>"><a href="#" onclick="removeOption(<?=$opt->getOptionID()?>)"><img src="<?=ASSETS_URL_IMAGES?>/icons/delete_small.png" /></a> <?=$opt->getOptionName()?>
        <input type="hidden" name="survivingOptionNames[]" value="<?=$opt->getOptionName()?>" />
        </div>		
	<? }

} ?>
</div>
<br/><br/>
<strong>Add option</strong><br/>

<input type="text" name="optionValue" id="ccm-survey-optionValue" style="width: 350px" />
<input type="button" onclick="addOption()" value="add" />