<?php defined('C5_EXECUTE') or die("Access Denied.");
/* @var $h ConcreteDashboardHelper */
$h = Loader::helper('concrete/dashboard');
/* @var $ih ConcreteInterfaceHelper */
$ih = Loader::helper('concrete/interface');
/* @var $form FormHelper */
$form = Loader::helper('form');
/* @var $jh JsonHelper */
$jh = Loader::helper('json');

?>
<style type="text/css">
#ccm-jobs-list td {
	vertical-align: middle;
	-webkit-transition-property: color, background-color;
	-webkit-transition-duration: .9s, .9s;
	-moz-transition-property: color, background-color;
	-moz-transition-duration: .9s, .9s;
	-o-transition-property: color, background-color;
	-o-transition-duration: .9s, .9s;
	-ms-transition-property: color, background-color;
	-ms-transition-duration: .9s, .9s;
	transition-property: color, background-color;
	transition-duration: .9s, .9s;
 }

#ccm-jobs-list td button {
 	float: right;
 }

#ccm-jobs-list tr.error td {
	color: #f00;
}

#ccm-jobs-list tr.success td {
	color: #090;
}

</style>

<?=$h->getDashboardPaneHeaderWrapper(t('Automated Jobs'), false, false);?>

<?=Loader::helper('concrete/interface')->tabs(array(
	array('list', t('Jobs'), $jobListSelected),
	array('sets', t('Job Sets'), $jobSetsSelected)
));?>

<div id="ccm-tab-content-list" <? if (!$jobListSelected) { ?>style="display: none" <? } ?>>

<? if (count($installedJobs) > 0) { ?>

<table class="table" id="ccm-jobs-list">
	<thead>
	<tr>
		<th><?=t('ID')?></th>
		<th style="width: 200px"><?=t('Name')?></th>
		<th><?=t('Last Run')?></th>
		<th style="width: 200px"><?=t('Results of Last Run')?></th>
		<td><a href="<?=$this->action('reset')?>" class="btn pull-right btn-mini"><?=t('Reset All Jobs')?></a></td>
	</tr>
	</thead>
	<tbody>
	<? foreach($installedJobs as $j) { ?>
		<tr class="<? if ($j->didFail()) { ?>error<? } ?> <? if ($j->getJobStatus() == 'RUNNING') {?>running<? } ?>">
			<td><?=$j->getJobID()?></td>
			<td><i class="icon-question-sign" title="<?=$j->getJobDescription()?>"></i> <?=$j->getJobName()?></td>
			<td class="jDateLastRun"><?
				if ($j->getJobStatus() == 'RUNNING') {
					$runtime = date(DATE_APP_GENERIC_MDYT_FULL_SECONDS, strtotime($j->getJobDateLastRun()));
					echo ("<strong>");
					echo t("Running since %s", $runtime);					
					echo ("</strong>");
				} else if($j->getJobDateLastRun() == '' || substr($j->getJobDateLastRun(), 0, 4) == '0000') {
					echo t('Never');
				} else {
					$runtime = date(DATE_APP_GENERIC_MDYT_FULL_SECONDS, strtotime($j->getJobDateLastRun()) );
					echo $runtime;
				}
			?></td>
			<td class="jLastStatusText"><?=$j->getJobLastStatusText()?></td>
			<td class="ccm-jobs-button"><button data-jID="<?=$j->getJobID()?>" data-jSupportsQueue="<?=$j->supportsQueue()?>" data-jName="<?=$j->getJobName()?>" class="btn-run-job btn-small btn"><i class="icon-play"></i> <?=t('Run')?></button></td>
		</tr>

	<? } ?>
	</tbody>
</table>

<? } else { ?>
	<p><?=t('You have no jobs installed.')?></p>
<? } ?>

<? if (count($availableJobs) > 0) { ?>
	<h4><?=t('Awaiting Installation')?></h4>
	<table class="table table-striped">
	<thead>
		<tr> 
			<th><?=t('Name')?></th>
			<th><?=t('Description')?></th> 
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?foreach($availableJobs as $availableJobName => $job):?>
		<tr> 
			<td><?=$job->getJobName() ?></td>
			<td><?=$job->getJobDescription() ?></td> 
			<td><?if(!$job->invalid):?>
				<a href="<?=$this->action('install', $job->jHandle)?>" class="btn btn-small pull-right"><?=t('Install')?></a>
			<?endif?></td>
		</tr>	
		<?endforeach?>
	</tbody>
	</table>
<? } ?>
<? 
$djs = JobSet::getDefault();
if (is_object($djs)) { ?>
<div class="well">
<h4><?=t('Automation Instructions')?></h4>
<p><?=t('To run all the jobs in the <a href="%s">%s</a> Job Set, schedule this URL using cron or a similar system:', $this->url('/dashboard/system/optimization/jobs', 'edit_set', $djs->getJobSetID()), $djs->getJobSetName())?></p>
<div><input type="text" style="width: 700px" class="ccm-default-jobs-url" value="<?=BASE_URL . $this->url('/tools/required/jobs?auth=' . $auth)?>" /></div>
</div>
<? } ?>

</div>

<div id="ccm-tab-content-sets" <? if (!$jobSetsSelected) { ?>style="display: none" <? } ?>>


<?php if (in_array($this->controller->getTask(), array('update_set', 'update_set_jobs', 'edit_set', 'delete_set'))) { ?>


		<div class="row">
		<div class="span-pane-half">

		<form class="form-vertical" method="post" action="<?php echo $this->action('update_set')?>">
			
			<input type="hidden" name="jsID" value="<?php echo $set->getJobSetID()?>" />

			<?php echo Loader::helper('validation/token')->output('update_set')?>

		<fieldset>
			<legend><?=t('Details')?></legend>

			<div class="control-group">
				<?php echo $form->label('jsName', t('Name'))?>
				<div class="controls">
					<?php echo $form->text('jsName', $set->getJobSetName())?>
				</div>
			</div>

			<div class="control-group">
				<label></label>
				<div class="controls">
					<?php echo $form->submit('submit', t('Update Set'), array('class' => ''))?>
				</div>
			</div>
		</fieldset>
		</form>


		<? if ($set->canDelete()) { ?>

		<form method="post" action="<?php echo $this->action('delete_set')?>" class="form-vertical">
		<fieldset>
			<legend><?=t('Delete Set')?></legend>
			<div class="control-group">
			<div class="controls">
				<p><?php echo t('Warning, this cannot be undone. No jobs will be deleted but they will no longer be grouped together.')?></p>
			</div>
			</div>
			
			<input type="hidden" name="jsID" value="<?php echo $set->getJobSetID()?>" />
			<?php echo Loader::helper('validation/token')->output('delete_set')?>		
			<div class="clearfix">
				<?php echo $form->submit('submit', t('Delete Job Set'), array('class' => 'danger'))?>
			</div>
		</form>
		<? } ?>
		</div>

		<div class="span-pane-half">
	
		<form class="form-vertical" method="post" action="<?php echo $this->action('update_set_jobs')?>">
			<input type="hidden" name="jsID" value="<?php echo $set->getJobSetID()?>" />
			<?php echo Loader::helper('validation/token')->output('update_set_jobs')?>

		<fieldset>
			<legend><?=t('Jobs')?></legend>
			
	
			<?php 
			$list = $set->getJobs();
			if (count($installedJobs) > 0) { ?>
	
				<div class="control-group">
					<div class="controls">
	
						<?php foreach($installedJobs as $g) { 	

						?>
								<label class="checkbox">
									<?php echo $form->checkbox('jID[]', $g->getJobID(), $set->contains($g)) ?>
									<span><?php echo $g->getJobName()?></span>
								</label>
						<?php } ?>
					</div>
				</div>
		
				<div class="control-group">
					<div class="controls">
					<?php echo $form->submit('submit', t('Update Jobs'), array('class' => ''))?>
					</div>
				</div>
			<?php } else { ?>
				<div class="control-group">
					<div class="controls">
						<p><?php echo t('No Jobs found.')?></p>
					</div>
				</div>
			<?php } ?>
		</fieldset>
		</form>
		</div>
	</div>

		<div class="well">
		<h4><?=t('Automation Instructions')?></h4>
		<p><?=t('To run all the jobs in this Job Set, schedule this URL using cron or a similar system:', $set->getJobSetID())?></p>
		<div><input type="text" style="width: 700px" class="ccm-default-jobs-url" value="<?=BASE_URL . $this->url('/tools/required/jobs?auth=' . $auth . '&jsID=' . $set->getJobSetID())?>" /></div>
		</div>

<? } else { ?>

	<form method="post" class="form-horizontal" action="<?php echo $this->action('add_set')?>">


	<?php if (count($jobSets) > 0) { ?>
	
		<div class="ccm-attribute-sortable-set-list">
		
			<?php foreach($jobSets as $j) { ?>
				<div class="ccm-group" id="asID_<?php echo $j->getJobSetID()?>">
					<a class="ccm-group-inner" href="<?php echo $this->url('/dashboard/system/optimization/jobs', 'edit_set', $j->getJobSetID())?>" style="background-image: url(<?php echo ASSETS_URL_IMAGES?>/icons/group.png)"><?php echo $j->getJobSetName()?></a>
				</div>
			<?php } ?>
		</div>
	
	<?php } else { ?>
		<p><?php echo t('You have not added any Job sets.')?></p>
	<?php } ?>

	<br/>
	
	<h4><?=t('Add Set')?></h4>

	<?php echo Loader::helper('validation/token')->output('add_set')?>
	<div class="control-group">
		<?php echo $form->label('jsName', t('Name'))?>
		<div class="controls">
			<?php echo $form->text('jsName')?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"><?=t('Jobs')?></label>
		<div class="controls">
		<? foreach($installedJobs as $j) { ?>
			<label class="checkbox"><?=$form->checkbox('jID[]', $j->getJobID())?> <span><?=$j->getJobName()?></span></label>			
		<? } ?>
		</div>
	</div>
	
	<div class="control-group">
		<label></label>
		<div class="controls">
			<?php echo $form->submit('submit', t('Add Job Set'), array('class' => 'btn'))?>
		</div>
	</div>

	</form>

	<? } ?>
</div>


<script type="text/javascript">

var pulseRowInterval = false;

jQuery.fn.showLoading = function() {
	if ($(this).find('button').attr('data-jSupportsQueue')) {
		$(this).find('button').html('<i class="icon-refresh"></i> <?=t('View')?>');
	} else {
		$(this).find('button').html('<i class="icon-refresh"></i> <?=t('Run')?>').prop('disabled', true);
	}
	var row = $(this);
	row.removeClass('error success');

	if (!row.attr('data-color')) {
		row.find('td').css('background-color', '#ccc');
	}
	pulseRowInterval = setInterval(function() {
		if (row.attr('data-color') == '#ccc') {
			row.find('td').css('background-color', '#fff');
			row.attr('data-color', '#fff');
		} else {
			row.find('td').css('background-color', '#ccc');
			row.attr('data-color', '#ccc');
		}			
	}, 500);
}

jQuery.fn.hideLoading = function() {
	$(this).find('button').html('<i class="icon-play"></i> <?=t('Run')?>').prop('disabled', false);
	var row = $(this);
	row.removeClass();
	row.find('td').css('background-color', '');
	row.attr('data-color', '');
	clearInterval(pulseRowInterval);
}

jQuery.fn.processResponse = function(r) {
	$(this).hideLoading();
	if (r.error) {
		$(this).addClass('error');
	} else {
		$(this).addClass('success');
	}
	$(this).find('.jDateLastRun').html(r.jDateLastRun);
	$(this).find('.jLastStatusText').html(r.result);
}

$(function() {
	$('tr.running').showLoading();
	$('.ccm-default-jobs-url').on('click', function() {
		$(this).get(0).select();
	});
	$('.icon-question-sign').tooltip();
	$('.btn-run-job').on('click', $('#ccm-jobs-list'), function() {
		var row = $(this).parent().parent();
		row.showLoading();
		var jSupportsQueue = $(this).attr('data-jSupportsQueue');
		var jID = $(this).attr('data-jID');
		var jName = $(this).attr('data-jName');
		var params = [
			{'name': 'auth', 'value': '<?=$auth?>'},
			{'name': 'jID', 'value': jID}
		];
		if (jSupportsQueue) {
			ccm_triggerProgressiveOperation(
				CCM_TOOLS_PATH + '/jobs/run_single',
				params,
				jName, function(r) {
					$('.ui-dialog-content').dialog('close');
					row.processResponse(r);
				}, function(r) {
					row.processResponse(r);
				}
			);
		} else {
			$.ajax({ 
				url: CCM_TOOLS_PATH + '/jobs/run_single',
				data: params,
				dataType: 'json',
				cache: false,
				success: function(json) {
					row.processResponse(json);
				}
			});
		}
	});
});
</script>
<?=$h->getDashboardPaneFooterWrapper();?>