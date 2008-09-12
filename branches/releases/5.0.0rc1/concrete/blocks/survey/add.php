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

<strong>Question</strong><br/>
<input type="text" style="width: 350px" name="question" value="" />
<br><br>
<strong>Open to all site visitors?</strong><br/>
<input type="radio" value="0" name="requiresRegistration" style="vertical-align: middle" checked />&nbsp;Yes
&nbsp;&nbsp;
<input type="radio" value="1" name="requiresRegistration" style="vertical-align: middle" />&nbsp;No (requires registration to answer)
<br><br>
<strong>Options</strong>
<div id="pollOptions">
None
</div>

<br/><br/>
<strong>Add option</strong><br/>
<input type="text" name="optionValue" id="ccm-survey-optionValue" style="width: 350px" />
<input type="button" onclick="addOption()" value="Add" />