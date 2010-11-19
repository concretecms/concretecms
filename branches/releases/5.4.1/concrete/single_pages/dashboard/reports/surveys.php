<?php 
defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php  if ($this->controller->getTask() == 'viewDetail') { ?>
	<h1><span><?php echo t('Results for &#34;%s&#34;', $current_survey)?></span></h1>
	<div class="ccm-dashboard-inner">
	
	<div class="surveyOverview" >
		<div id="displayOptions">
			<a href="<?php echo $this->action('view')?>">&#60;&#60; Back to List</a>
		</div>
		<table class="entry-form" >
			<tr>
				<td class="header"><?php echo t('Option')?></td>
				<td class="header"><?php echo t('IP Address')?></td>
				<td class="header"><?php echo t('Date')?></td>
				<td class="header"><?php echo t('User')?></td>
			</tr>
			<?php  
			foreach($survey_details as $detail) { ?>
			<tr>
				<td><?php echo $detail['option'] ?></td>
				<td><?php echo $detail['ipAddress'] ?></td>
				<td><?php echo $detail['date'] ?></td>
				<td><?php echo $detail['user'] ?></td>
			</tr>
		<?php  } ?>
	</table>
	</div>
	
		<div>
			<?php echo  $chart_options ?>
			<?php echo  $pie_chart ?>
		</div>
		<div style="clear: both;"></div>
	</div>
	</div>

<?php  } else { ?>

<h1><span><?php echo t('Surveys')?></span></h1>
<div class="ccm-dashboard-inner">
	
	<?php  if (count($surveys) == 0) { ?>
	<?php echo t('You have not created any surveys.')?>
	<?php  } else { ?>
	
	<div class="surveyDetails">

		<table class="entry-form" >
			<tr>
				<td class="header"><a href="<?php echo $surveyList->getSortByURL('question', 'asc')?>"><?php echo t('Name')?></a></td>
				<td class="header"><a href="<?php echo $surveyList->getSortByURL('cvName', 'asc')?>"><?php echo t('Found on Page')?></a></td>
				<td class="header"><a href="<?php echo $surveyList->getSortByURL('lastResponse', 'desc')?>"><?php echo t('Last Response')?></a></td>
				<td class="header"><a href="<?php echo $surveyList->getSortByURL('numberOfResponses', 'desc')?>"><?php echo t('Number of Responses')?></a></td>
			</tr>
			<?php  foreach($surveys as $survey) { ?>
					<tr>
						<td><a href="<?php echo $this->action('viewDetail', $survey['bID'], $survey['cID'])?>"><?php echo $survey['question'] ?></a></td>
						<td><?php echo $survey['cvName'] ?></td>
						<td><?php echo formatDate($survey['lastResponse']) ?></td>
						<td><?php echo $survey['numberOfResponses'] ?></td>
					</tr>
				<?php  }
			} ?>
		</table>
		
		<?php  $surveyList->displayPaging(); ?>
	</div>
	
	
	</div>
</div>




<?php  } ?>