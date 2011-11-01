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
#run-all, .run-task { height: 16px; width: 16px; display: block; }

.run-task {
	background: url("/concrete/images/ui-icons_222222_256x240.png") no-repeat 0 -160px transparent;
}

#run-all {
	background: url("/concrete/images/ui-icons_222222_256x240.png") no-repeat -32px -160px transparent;
}
</style>
<script>
jQuery(function($) {
	$('.btn.remove').bind('click', function(e) {
		if (!confirm(<?=$jh->encode(t("Are you sure you want to uninstall this job?"))?>)) {
			e.preventDefault();
		}
	});
	
});
</script>
<?=$h->getDashboardPaneHeaderWrapper(t('Automated Jobs'), false, false, false);?>
<form id="automated-jobs-run-form" action="<?=$this->action('')?>" method="post">
<?=$this->controller->token->output('automated_jobs')?>
<div class="ccm-pane-body">
<?if ($jobList->numRows() == 0):?>
<?=t('You currently have no jobs installed.')?>
<?else:?>
<table class="zebra-striped">
<thead>
<tr>
	<th><a id="run-all" href="<?=$this->action('execute_all')?>" title="<?=t('Run all')?>"></a></th>
	<th><?=t('ID')?></th>
	<th><?=t('Name')?></th>
	<th><?=t('Description')?></th>
	<th><?=t('Last Run')?></th>
	<th><?=t('Results of Last Run')?></th>
	<th></th>
</tr>
</thead>
<tbody>
<?foreach ($jobList as $job):?>
<tr>
	<td><a class="run-task" title="<?=t('Run')?>" href="<?=$this->action('execute', $job['jID'])?>"></a></td>
	<td><?=$job['jID']?></td>
	<td><?=$job['jName']?></td>
	<td><?=$job['jDescription']?></td>
	<td><?
	if ($job['jStatus'] == 'RUNNING') {
		$runtime = date(DATE_APP_GENERIC_TS, strtotime($job['jDateLastRun']));
		echo ("<strong>");
		echo t("Currently Running (Since %s)", $runtime);					
		echo ("</strong>");
	} else if($job['jDateLastRun'] == '' || substr($job['jDateLastRun'], 0, 4) == '0000') {
		echo t('Never');
	} else {
		$runtime = date(DATE_APP_GENERIC_MDY . t(' \a\t ') . DATE_APP_GENERIC_TS, strtotime($job['jDateLastRun']) );
		echo $runtime;
	}
?></td>
	<td><?=$job['jLastStatusText']?></td>
	<td><?if(!$job['jNotUninstallable']):?>
		<a class="btn remove" href="<?=$this->action('uninstall', $job['jID'])?>"><?=t('Remove')?></a>
	<?endif?></td>
</tr>
<?endforeach?>
</tbody>
</table>
<?endif?>

<?if($availableJobs):?>
<h2><?=t('Jobs Available for Installation')?></h2>
<table class="zebra-striped">
<thead>
	<tr> 
		<th><?=t('Name')?></td>
		<th><?=t('Description')?></td> 
		<th></td>
	</tr>
</thead>
<tbody>
	<?foreach($availableJobs as $availableJobName => $job):?>
	<tr> 
		<td><?=$job->getJobName() ?></td>
		<td><?=$job->getJobDescription() ?></td> 
		<td><?if(!$job->invalid):?>
			<a class="btn" href="<?=$this->action('install', $job->jHandle)?>"><?=t('Install')?></a>
		<?endif?></td>
	</tr>	
	<?endforeach?>
</tbody>
</table>
<?endif?>
</div>
<div class="ccm-pane-footer">
<?
	$submit = $ih->submit( t('Save'), 'tracking-code-form', 'right', 'primary');
	print $submit;
?>
</div>
</form>
<?=$h->getDashboardPaneFooterWrapper(false);?>
<?return;?>
<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<script type="text/javascript">

var Jobs = {

	pendingJobs:[],
	
	runChecked : function (){
		this.pendingJobs=[];
		$('#runJobsButton').attr('disabled',true);
		var jobsCBs=$('.runJobCheckbox');
		for( var i=0; i<jobsCBs.length; i++ ){ 
			jobsCBs[i].disabled=true;
			if( !jobsCBs[i].checked ) continue; 
			this.pendingJobs.push( jobsCBs[i].value ); 
		}
		if( this.pendingJobs.length==0 )
			alert("<?=t("Please first check jobs you want to run.")?>");
		this.runNextPending();
	},
	
	runNextPending : function (){
		if(this.pendingJobs.length==0){
			$('.runJobCheckbox').attr('disabled',false);
			$('#runJobsButton').attr('disabled',false);
			return;
		}
		var jID=this.pendingJobs.shift();
		$('#jobItemRow'+jID).addClass('running');
		$.ajax({ 
			url: CCM_TOOLS_PATH+'/jobs?auth=<?=$auth?>&jID='+jID,
			success: function(json){
				eval('var jObj='+json);
				$('#jLastStatusText'+jObj.jID).html(jObj.message);
				$('#jDateLastRun'+jObj.jID).html(jObj.jDateLastRun);
				var r=$('#jobItemRow'+jObj.jID)
				r.removeClass('running');
				r.removeClass('runSuccess');
				r.removeClass('runError');
				if(jObj.error==0) r.addClass('runSuccess');
				else r.addClass('runError');
				Jobs.runNextPending();
			}
		});
	},
	
	changeStatus:function(cb){
		if(cb.checked) var jStatus='ENABLED';
		else var jStatus='DISABLED';
		$.ajax({  url: CCM_TOOLS_PATH+'/tools/required/jobs?auth=<?=$auth?>&jID='+cb.value+'&jStatus='+jStatus  });
	},
	
	confirmUninstall:function(){
		if( confirm('<?=t("Are you sure you want to uninstall this job?")?>') )
			return true;
		else return false;
	},
	
	selectAll : function(){  
		$('.runJobCheckbox').each(function(num,el){ 
			el.checked=true;
			Jobs.changeStatus(el);
		})  
	},	
	selectNone: function(){  
		$('.runJobCheckbox').each(function(num,el){
		el.checked=false;
		Jobs.changeStatus(el);
		})  
	}
}

</script>

<style type="text/css">
tr div.runningThrobber{ background:#f00; display:none; width:80px; margin:auto }
tr.running div.runningThrobber{ background:#f00; display:block; background: url(<?=ASSETS_URL_IMAGES?>/dashboard/sitemap/loading.gif) no-repeat center; height:20px }
tr .runJobCheckboxWrap{ height:20px; width:80px; margin:auto;  }
tr.running .runJobCheckboxWrap{ display:none; }
tr .runJobCheckboxWrap .notificationIcon{ float:right; height:20px; width:20px; display:none; right:0px; }
tr.runSuccess .runJobCheckboxWrap .notificationIcon{ background:url(<?=ASSETS_URL_IMAGES?>/icons/success.png) no-repeat right; display:block; }
tr.runError .runJobCheckboxWrap .notificationIcon{ background:url(<?=ASSETS_URL_IMAGES?>/icons/warning.png) no-repeat right; display:block;}
tr.runError .jLastStatusText{ color:#dd2222 }

div.ccm-button{ float:right}
div.ccm-buttons{ position:absolute; right:8px; top:8px; }
</style>

<h1><span><?=t('Scheduled Jobs')?></span></h1>

<div class="ccm-dashboard-inner">


<? if(  $jobListRS->numRows() == 0 ){ ?>
	
	<div style="margin:16px 0px"><strong><?=t('You currently have no jobs installed.')?></strong></div>

<? }else{

$ih = Loader::helper('concrete/interface');

?>

	<?
		$b1 = $ih->button_js(t('Run Checked'), 'Jobs.runChecked()');
		//print $ih->buttons($b1);
		print '<div class="ccm-buttons"><a onclick="Jobs.runChecked()" href="javascript:void(0)"><div class="ccm-button"><span>'.t('Run Checked').'</span></div></a></div>';
	?>
	
	<h2 style="padding-bottom:8px; padding-top:16px"><?=t('Installed Jobs')?></h2>
	
	<div class="ccm-spacer">&nbsp;</div>
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table class="grid-list" width="100%" cellspacing="1" cellpadding="0" border="0">
	
		<tr>
			<td class="subheader center" >
				<a href="javascript:void(0)" onclick="Jobs.selectAll()"><?=t('All')?></a> | <a href="javascript:void(0)" onclick="Jobs.selectNone()"><?=t('None')?></a>
			</td>
			<td class="subheader"><?=t('ID')?></td>
			<td class="subheader"><?=t('Name')?></td>
			<td class="subheader"><?=t('Description')?></td>
			<td class="subheader"><?=t('Last Run')?></td>
			<td class="subheader"><?=t('Results of Last Run')?></td>
			<td class="subheader" >&nbsp;</td>
		</tr>
		
		<? while( $jobItem = $jobListRS->fetchRow() ){ ?>
		<tr id="jobItemRow<?=$jobItem['jID']?>" 
			class="<?=($jobItem['jStatus']=='RUNNING')?'running':''?> <?=( $jobItem['jStatus']=='DISABLED_ERROR' )?'runError':''?>">
			<td class="center" >
				<div class="runningThrobber">&nbsp;</div>
				<div class="runJobCheckboxWrap">
				<div class="notificationIcon">&nbsp;</div>
				<input name="runJobCheckbox" class="runJobCheckbox" type="checkbox" value="<?=$jobItem['jID']?>"
					<? if($jobItem['jStatus']=='ENABLED')echo 'checked="checked"' ?>  
					onchange="Jobs.changeStatus(this)" />
				</div>
			</td>
			<td><?=$jobItem['jID']?></td>
			<td><?=$jobItem['jName']?></td>
			<td><?=$jobItem['jDescription']?></td>
			<td id="jDateLastRun<?=$jobItem['jID']?>">
				<?
				if($jobItem['jStatus']=='RUNNING'){
					$runtime=date(DATE_APP_GENERIC_TS, strtotime($jobItem['jDateLastRun']) );
					echo ("<strong>");
					echo t("Currently Running (Since %s)",$runtime);					
					echo ("</strong>");
				}elseif($jobItem['jDateLastRun'] == '' || substr($jobItem['jDateLastRun'],0,4)=='0000'){
					echo t('Never');
				}else{
					$runtime=date(DATE_APP_GENERIC_MDY . t(' \a\t ') . DATE_APP_GENERIC_TS, strtotime($jobItem['jDateLastRun']) );
					echo $runtime;
				}
				?>
			</td>
			<td id="jLastStatusText<?=$jobItem['jID']?>" class="jLastStatusText"><?=$jobItem['jLastStatusText']?></td>
			<td class="center">
				<? if(!$jobItem['jNotUninstallable']){ ?>
				<form method="post" action="<?=$this->url('/dashboard/system/jobs', 'uninstall')?>" onsubmit="return Jobs.confirmUninstall();">
					<input name="jID" type="hidden" value="<?=$jobItem['jID'] ?>" />
					<input name="Remove" type="Submit" value="<?=t('Remove')?>" />
				</form>
				<? } ?>
			</td>
		</tr>	
		<? } ?>

	</table>
	</div>
	
<? } ?>


<? if (count($availableJobs) > 0) { ?>
	

	<br/>
	
	<h2><?=t('Jobs Available for Installation')?></h2>
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table class="grid-list" width="100%" cellspacing="1" cellpadding="0">
		<tr> 
			<td class="subheader"><?=t('Name')?></td>
			<td class="subheader"><?=t('Description')?></td> 
			<td class="subheader" width="10%">&nbsp;</td>
		</tr>
		
		<? foreach( $availableJobs as $availableJobName=>$availableJobObj ){ ?>
		<tr> 
			<td><?=$availableJobObj->getJobName() ?></td>
			<td><?=$availableJobObj->getJobDescription() ?></td> 
			<td class="center">
				<? if(!$availableJobObj->invalid){ ?>
				<form method="post" action="<?=$this->url('/dashboard/system/jobs', 'install')?>">
					<input name="jHandle" type="hidden" value="<?=$availableJobObj->jHandle ?>" />
					<input name="Install" type="Submit" value="Install" />
				</form>
				<? }else echo '&nbsp;'; ?>
			</td>
		</tr>	
		<? } ?>
	
	</table>
	</div>
	
<? } ?>

<br/><br/>
<?=t('If you wish to run these jobs in the background, automate access to the following URL:')?>
<br/><br/>
<code>
<?=BASE_URL . $this->url('/tools/required/jobs?auth=' . $auth)?>
</div>