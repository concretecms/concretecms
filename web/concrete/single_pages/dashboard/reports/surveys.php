<?php
defined('C5_EXECUTE') or die("Access Denied."); 

// Helpers
$ih = Loader::helper('concrete/interface');

// Content
if ($this->controller->getTask() == 'viewDetail') { ?>

    <?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Results for &#34;%s&#34;', $current_survey), false, false, false);?>
    
	<div class="ccm-pane-body">
    
    	<div class="row">
    
          <div class="span10">
      
            <table class="table table-striped">
              <thead>
                <tr>
                    <th><?=t('Option')?></th>
                    <th><?=t('IP Address')?></th>
                    <th><?=t('Date')?></th>
                    <th><?=t('User')?></th>
                </tr>
              </thead>
              <tbody>
                <? 
                foreach($survey_details as $detail) { ?>
                <tr>
                    <td><?=$detail['option'] ?></td>
                    <td><?=$detail['ipAddress'] ?></td>
                    <td><?=$detail['date'] ?></td>
                    <td><?=$detail['user'] ?></td>
                </tr>
              <? } ?>
              </tbody>
            </table>
        
          </div>
          
          <div class="span5" style="margin-left:30px;">
      
            <div style="text-align:center;">
              <?= $pie_chart ?>
              <?= $chart_options ?>              
            </div>
        
          </div>
        
        </div>
        
	</div>
    
    <div class="ccm-pane-footer">
        <? print $ih->button(t('Back to List'), $this->action('view'), 'left'); ?>
    </div>
    
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

<? } else { ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Surveys'), false, false);?>
	
	<? if (count($surveys) == 0) { ?>
	<?= "<p>".t('You have not created any surveys.')."</p>" ?>
	<? } else { ?>

		<table class="table table-striped">
        	<thead>
                <tr>
                    <th class="<?=$surveyList->getSearchResultsClass('question')?>"><a href="<?=$surveyList->getSortByURL('question', 'asc')?>"><?=t('Name')?></a></th>
                    <th class="<?=$surveyList->getSearchResultsClass('cvName')?>"><a href="<?=$surveyList->getSortByURL('cvName', 'asc')?>"><?=t('Found on Page')?></a></th>
                    <th class="<?=$surveyList->getSearchResultsClass('lastResponse')?>"><a href="<?=$surveyList->getSortByURL('lastResponse', 'desc')?>"><?=t('Last Response')?></a></th>
                    <th class="<?=$surveyList->getSearchResultsClass('numberOfResponses')?>"><a href="<?=$surveyList->getSortByURL('numberOfResponses', 'desc')?>"><?=t('Number of Responses')?></a></th>
                </tr>
            </thead>
            <tbody>
			<? foreach($surveys as $survey) { ?>
					<tr>
						<td><strong><a href="<?=$this->action('viewDetail', $survey['bID'], $survey['cID'])?>"><?=$survey['question'] ?></a></strong></td>
						<td><?=$survey['cvName'] ?></td>
						<td><?=formatDate($survey['lastResponse']) ?></td>
						<td><?=$survey['numberOfResponses'] ?></td>
					</tr>
				<? }
			} ?>
            </tbody>
		</table>
		
		<? $surveyList->displayPagingV2(); ?>
    
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>

<? } ?>