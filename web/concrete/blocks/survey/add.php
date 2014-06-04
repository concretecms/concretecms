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
<div class="form-group">
    <label for="questionEntry"><?=t('Question')?></label>
    <input type="text" style="width: 320px" name="question" value="" class="form-control" />
</div>
<label for="requiresRegistration"><?=t('Target Audience')?></label>
<div class="radio">
    <label>
        <input id="requiresRegistration" type="radio" value="0" name="requiresRegistration" style="vertical-align: middle" checked />&nbsp;<?=t('Public')?>
    </label>
</div>
<div class="radio">
    <label>
        <input type="radio" value="1" name="requiresRegistration" style="vertical-align: middle" />&nbsp;<?=t('Only Registered Users')?>
    </label>
</div>
<hr />
<label><?=t('Answer Options')?></label>
<div class="form-group">
    <div id="pollOptions">
    <?=t('None')?>
    </div>
</div>
<label for="optionEntry"><?=t('Add Option')?></label>
<div class="form-group">
    <div class="input-group">
        <input type="text" name="optionValue" id="ccm-survey-optionValue" class="form-control"/>
    <span class="input-group-btn">
    <button class="btn btn-default" type="button" value="Add" onclick="addOption()"><?php echo t('Add'); ?></button>
    </span>
    </div>
</div>
</div>