<?php
// Survey single page
defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<!-- TODO:
	- Don't use JS to toggle between modes. Use 2 separate pages.
-->

<!-- Temporary: CSS and JS here until done debugging -->
<style type="text/css" rel="stylesheet">
div.surveySwatch {
	background: #999999 none repeat scroll 0 50%;
	border: 1px solid #666666;
	float: left;
	font-size: 1px;
	height: 10px;
	line-height: 1px;
	margin: 3px 4px 0 0;
	width: 10px;
}
</style>

<script type="text/javascript">
function show_survey_detail(bID) {
	// Make ajax call to load data into survey_details
	$("#survey_main").css("display", "none");
	$("#survey_details").css("display", "block");
}

function go_back() {
	$("#survey_main").css("display", "block");
	$("#survey_details").css("display", "none");
}
</script>

<div id="survey_main">
	<h1><span><?=t('Surveys')?></span></h1>
	<div class="ccm-dashboard-inner">
	<? if (count($surveys) == 0) { ?>
	<?=t('You have not created any surveys.')?>
	<? } else { ?>

	<div style="margin:0px; padding:0px; width:100%; height:auto">
	<table class="entry-form" >
		<tr>
			<td class="header"><?=t('Name')?></td>
			<td class="header"><?=t('Found on Page')?></td>
			<td class="header"><?=t('Last Response')?></td>
			<td class="header"><?=t('Number of Responses')?></td>
		</tr>
		<? foreach($surveys as $survey) { ?>
				<tr>
					<td><a href="javascript::show_survey_detail(<?= $survey['bID'] ?>);"><?=$survey['name'] ?></a></td>
					<td><?=$survey['foundOnPage'] ?></td>
					<td><?=$survey['lastResponse'] ?></td>
					<td><?=$survey['numberOfResponses'] ?></td>
				</tr>
			<? }
		} ?>
	</table>
	</div>
	</div>
</div>

<div id="survey_details">
	<h1><span><?=t('Results for &#34;' . $surveys[32]['name'] . '&#34;')?></span></h1>
	<div class="ccm-dashboard-inner">

	<div style="margin:0px; padding:0px; width:60%; height:auto; float: left;" >
	<a href="#">&#60;&#60; Back to List</a>
	<table class="entry-form" >
		<tr>
			<td class="header"><?=t('Option')?></td>
			<td class="header"><?=t('IP Address')?></td>
			<td class="header"><?=t('Date')?></td>
			<td class="header"><?=t('User')?></td>
		</tr>
		<? foreach($survey_details as $detail) { ?>
				<tr>
					<td><?=$detail['option'] ?></td>
					<td><?=$detail['ipAddress'] ?></td>
					<td><?=$detail['date'] ?></td>
					<td><?=$detail['user'] ?></td>
				</tr>
		<? } ?>
	</table>
	</div>

	<div>
		<?= $chart_options ?>
		<?= $pie_chart ?>
	</div>
</div>

<div style="clear: both;"></div>

</div>