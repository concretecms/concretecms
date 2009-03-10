<?php
defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<? if ($this->controller->getTask() == 'viewDetail') { ?>
	<h1><span><?=t('Results for &#34;' . $current_survey . '&#34;')?></span></h1>
	<div class="ccm-dashboard-inner">
	
	<div class="surveyOverview" >
		<div id="displayOptions">
			<a href="<?=$this->action('view')?>">&#60;&#60; Back to List</a>
		</div>
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
		<div id="displayOptions">
			Sort By:
			
			<? switch ($_GET['sortBy']) {
				case 'name': ?>
				<strong>
					<a href="?sortBy=name<?= isset($_GET['dir']) ? '&dir=' . $_GET['dir'] : '' ?>">Name</a>
				</strong> &#124;
				<a href="?sortBy=dateCreated<?= isset($_GET['dir']) ? '&dir=' . $_GET['dir'] : '' ?>">Date Created</a> &#124;
				<a href="?sortBy=numberOfResponses<?= isset($_GET['dir']) ? '&dir=' . $_GET['dir'] : '' ?>">Number of Responses</a>
			<? break; ?>	
			<? case 'numberOfResponses': ?>
				<a href="?sortBy=name<?= isset($_GET['dir']) ? '&dir=' . $_GET['dir'] : '' ?>">Name</a> &#124;
				<a href="?sortBy=dateCreated<?= isset($_GET['dir']) ? '&dir=' . $_GET['dir'] : '' ?>">Date Created</a> &#124;
				<strong>
					<a href="?sortBy=numberOfResponses<?= isset($_GET['dir']) ? '&dir=' . $_GET['dir'] : '' ?>">Number of Responses</a>
				</strong>	
			<? break; ?>
			<? default: ?>
				<a href="?sortBy=name<?= isset($_GET['dir']) ? '&dir=' . $_GET['dir'] : '' ?>">Name</a> &#124;
				<strong>
					<a href="?sortBy=dateCreated<?= isset($_GET['dir']) ? '&dir=' . $_GET['dir'] : '' ?>">Date Created</a>
				</strong> &#124;
				<a href="?sortBy=numberOfResponses<?= isset($_GET['dir']) ? '&dir=' . $_GET['dir'] : '' ?>">Number of Responses</a>
			<? break; ?>
			<? } ?>
			
			<div id="sortOptions">
				Order:
				<? switch ($_GET['dir']) {
				case 'asc': ?>
					<strong>
						<a href="<?= isset($_GET['sortBy']) ? '?sortBy=' . $_GET['sortBy'] . '&' : '?' ?>dir=asc">Ascending</a>
					</strong> &#124;
					<a href="<?= isset($_GET['sortBy']) ? '?sortBy=' . $_GET['sortBy'] . '&' : '?' ?>dir=desc">Descending</a>
				<? break; ?>
				<? default: ?>
					<a href="<?= isset($_GET['sortBy']) ? '?sortBy=' . $_GET['sortBy'] . '&' : '?' ?>dir=asc">Ascending</a> &#124;
					<strong>
						<a href="<?= isset($_GET['sortBy']) ? '?sortBy=' . $_GET['sortBy'] . '&' : '?' ?>dir=desc">Descending</a>
					</strong>
				<? break; ?>
				<? } ?>
				
			</div>
			
		</div>
		<table class="entry-form" >
			<tr>
				<td class="header"><?=t('Name')?></td>
				<td class="header"><?=t('Found on Page')?></td>
				<td class="header"><?=t('Last Response')?></td>
				<td class="header"><?=t('Number of Responses')?></td>
			</tr>
			<? foreach($surveys as $survey) { ?>
					<tr>
						<td><a href="<?=$this->action('viewDetail', $survey['bID'])?>"><?=$survey['name'] ?></a></td>
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




<? } ?>