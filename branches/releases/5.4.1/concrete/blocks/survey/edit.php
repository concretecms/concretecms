<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<style type="text/css">
div.survey-block-option {
	position: relative; border-bottom: 1px solid #ddd; padding-bottom: 3px; padding-top: 3px;
}

div.survey-block-option img {
	position: absolute; top: 3px; right: 0px;
}

</style>
<script type="text/javascript">
	var currentOption = <?php echo count($controller->options)?>;
</script>
<strong><?php echo t('Question')?></strong><br/>
<input type="text" style="width: 350px" name="question" value="<?php echo $controller->getQuestion()?>" />
<br><br>

<strong><?php echo t('Open to all site visitors?')?></strong><br/>
<input type="radio" value="0" name="requiresRegistration" style="vertical-align: middle" <?php  if (!$controller->requiresRegistration()) { ?> checked <?php  } ?> />&nbsp;<?php echo t('Yes')?>
&nbsp;&nbsp;
<input type="radio" value="1" name="requiresRegistration" style="vertical-align: middle" <?php  if ($controller->requiresRegistration()) { ?> checked <?php  } ?> />&nbsp;<?php echo t('No. Registration is required to answer.')?>
<br><br>

<strong><?php echo t('Options')?></strong>
<div id="pollOptions">
<?php  
$options = $controller->getPollOptions();
if (count($options) == 0) {
	echo t("None");
} else {
	foreach($options as $opt) { ?>		
        <div class="survey-block-option" id="option<?php echo $opt->getOptionID()?>"><a href="#" onclick="removeOption(<?php echo $opt->getOptionID()?>)"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/delete_small.png" /></a> <?php echo $opt->getOptionName()?>
        <input type="hidden" name="survivingOptionNames[]" value="<?php echo htmlspecialchars($opt->getOptionName())?>" />
        </div>		
	<?php  }
} ?>
</div>
<br/><br/>
<strong><?php echo t('Add option')?></strong><br/>

<input type="text" name="optionValue" id="ccm-survey-optionValue" style="width: 350px" />
<input type="button" onclick="addOption()" value="add" />