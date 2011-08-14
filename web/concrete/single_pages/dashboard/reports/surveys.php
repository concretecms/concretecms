<?php
defined('C5_EXECUTE') or die("Access Denied."); ?>

<? if ($this->controller->getTask() == 'viewDetail') { ?>
	<h1><span><?=t('Results for &#34;%s&#34;', $current_survey)?></span></h1>
	<div class="ccm-dashboard-inner">
	
	<div class="surveyOverview" >
		<div id="displayOptions">
			<a href="<?=$this->action('view')?>">&#60;&#60; <?=t('Back to List')?></a>
		</div>
		<table class="entry-form" >
			<tr>
				<td class="header"><?=t('Option')?></td>
				<td class="header"><?=t('IP Address')?></td>
				<td class="header"><?=t('Date')?></td>
				<td class="header"><?=t('User')?></td>
			</tr>
			<? 
			foreach($survey_details as $detail) { ?>
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
		<div style="clear: both;"></div>
	</div>
	</div>

<? } else { ?>

<h1><span><?=t('Surveys')?></span></h1>
<div class="ccm-dashboard-inner">
	
	<? if (count($surveys) == 0) { ?>
	<?=t('You have not created any surveys.')?>
	<? } else { ?>
	
	<div class="surveyDetails">

		<table class="entry-form" >
			<tr>
				<td class="header"><a href="<?=$surveyList->getSortByURL('question', 'asc')?>"><?=t('Name')?></a></td>
				<td class="header"><a href="<?=$surveyList->getSortByURL('cvName', 'asc')?>"><?=t('Found on Page')?></a></td>
				<td class="header"><a href="<?=$surveyList->getSortByURL('lastResponse', 'desc')?>"><?=t('Last Response')?></a></td>
				<td class="header"><a href="<?=$surveyList->getSortByURL('numberOfResponses', 'desc')?>"><?=t('Number of Responses')?></a></td>
			</tr>
			<? foreach($surveys as $survey) { ?>
					<tr>
						<td><a href="<?=$this->action('viewDetail', $survey['bID'], $survey['cID'])?>"><?=$survey['question'] ?></a></td>
						<td><?=$survey['cvName'] ?></td>
						<td><?=formatDate($survey['lastResponse']) ?></td>
						<td><?=$survey['numberOfResponses'] ?></td>
					</tr>
				<? }
			} ?>
		</table>
		
		<? $surveyList->displayPaging(); ?>
	</div>
	
	
	</div>
</div>




<? } ?>