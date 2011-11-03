<?php
defined('C5_EXECUTE') or die("Access Denied."); 

// Helpers
$ih = Loader::helper('concrete/interface');

// Content
if ($this->controller->getTask() == 'viewDetail') { ?>

    <?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Results for &#34;%s&#34;', $current_survey), false, false, false);?>
    
	<div class="ccm-pane-body">
    
    	<div class="ui-helper-clearfix">
    
          <div class="span9">
      
            <table class="zebra-striped">
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
          
          <div class="span5" style="margin-left:50px;">
      
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

		<table class="zebra-striped">
        	<thead>
                <tr>
                    <th><a href="<?=$surveyList->getSortByURL('question', 'asc')?>"><?=t('Name')?></a></th>
                    <th><a href="<?=$surveyList->getSortByURL('cvName', 'asc')?>"><?=t('Found on Page')?></a></th>
                    <th><a href="<?=$surveyList->getSortByURL('lastResponse', 'desc')?>"><?=t('Last Response')?></a></th>
                    <th><a href="<?=$surveyList->getSortByURL('numberOfResponses', 'desc')?>"><?=t('Number of Responses')?></a></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
			<? foreach($surveys as $survey) { ?>
					<tr>
						<td><strong><?=$survey['question'] ?></strong></td>
						<td><?=$survey['cvName'] ?></td>
						<td><?=formatDate($survey['lastResponse']) ?></td>
						<td><?=$survey['numberOfResponses'] ?></td>
                        <td><? print $ih->button(t('View Results'), $this->action('viewDetail', $survey['bID'], $survey['cID']),'left','small')?></td>
					</tr>
				<? }
			} ?>
            </tbody>
		</table>
		
		<? $surveyList->displayPaging(); ?>
    
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>

<? } ?>