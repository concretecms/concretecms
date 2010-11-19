<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<script>

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
			alert("<?php echo t("Please first check jobs you want to run.")?>");
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
			url: CCM_TOOLS_PATH+'/jobs?auth=<?php echo $auth?>&jID='+jID,
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
		$.ajax({  url: CCM_TOOLS_PATH+'/tools/required/jobs?auth=<?php echo $auth?>&jID='+cb.value+'&jStatus='+jStatus  });
	},
	
	confirmUninstall:function(){
		if( confirm('<?php echo t("Are you sure you want to uninstall this job?")?>') )
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
tr.running div.runningThrobber{ background:#f00; display:block; background: url(<?php echo ASSETS_URL_IMAGES?>/dashboard/sitemap/loading.gif) no-repeat center; height:20px }
tr .runJobCheckboxWrap{ height:20px; width:80px; margin:auto;  }
tr.running .runJobCheckboxWrap{ display:none; }
tr .runJobCheckboxWrap .notificationIcon{ float:right; height:20px; width:20px; display:none; right:0px; }
tr.runSuccess .runJobCheckboxWrap .notificationIcon{ background:url(<?php echo ASSETS_URL_IMAGES?>/icons/success.png) no-repeat right; display:block; }
tr.runError .runJobCheckboxWrap .notificationIcon{ background:url(<?php echo ASSETS_URL_IMAGES?>/icons/warning.png) no-repeat right; display:block;}
tr.runError .jLastStatusText{ color:#dd2222 }

div.ccm-button{ float:right}
div.ccm-buttons{ position:absolute; right:8px; top:8px; }
</style>

<h1><span><?php echo t('Scheduled Jobs')?></span></h1>

<div class="ccm-dashboard-inner">


<?php  if(  $jobListRS->numRows() == 0 ){ ?>
	
	<div style="margin:16px 0px"><strong><?php echo t('You currently have no jobs installed.')?></strong></div>

<?php  }else{

$ih = Loader::helper('concrete/interface');

?>

	<?php 
		$b1 = $ih->button_js(t('Run Checked'), 'Jobs.runChecked()');
		//print $ih->buttons($b1);
		print '<div class="ccm-buttons"><a onclick="Jobs.runChecked()" href="javascript:void(0)"><div class="ccm-button"><span>'.t('Run Checked').'</span></div></a></div>';
	?>
	
	<h2 style="padding-bottom:8px; padding-top:16px"><?php echo t('Installed Jobs')?></h2>
	
	<div class="ccm-spacer">&nbsp;</div>
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table class="grid-list" width="100%" cellspacing="1" cellpadding="0" border="0">
	
		<tr>
			<td class="subheader center" >
				<a href="javascript:void(0)" onclick="Jobs.selectAll()"><?php echo t('All')?></a> | <a href="javascript:void(0)" onclick="Jobs.selectNone()"><?php echo t('None')?></a>
			</td>
			<td class="subheader"><?php echo t('ID')?></td>
			<td class="subheader"><?php echo t('Name')?></td>
			<td class="subheader"><?php echo t('Description')?></td>
			<td class="subheader"><?php echo t('Last Run')?></td>
			<td class="subheader"><?php echo t('Results of Last Run')?></td>
			<td class="subheader" >&nbsp;</td>
		</tr>
		
		<?php  while( $jobItem = $jobListRS->fetchRow() ){ ?>
		<tr id="jobItemRow<?php echo $jobItem['jID']?>" 
			class="<?php echo ($jobItem['jStatus']=='RUNNING')?'running':''?> <?php echo ( $jobItem['jStatus']=='DISABLED_ERROR' )?'runError':''?>">
			<td class="center" >
				<div class="runningThrobber">&nbsp;</div>
				<div class="runJobCheckboxWrap">
				<div class="notificationIcon">&nbsp;</div>
				<input name="runJobCheckbox" class="runJobCheckbox" type="checkbox" value="<?php echo $jobItem['jID']?>"
					<?php  if($jobItem['jStatus']=='ENABLED')echo 'checked="checked"' ?>  
					onchange="Jobs.changeStatus(this)" />
				</div>
			</td>
			<td><?php echo $jobItem['jID']?></td>
			<td><?php echo $jobItem['jName']?></td>
			<td><?php echo $jobItem['jDescription']?></td>
			<td id="jDateLastRun<?php echo $jobItem['jID']?>">
				<?php 
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
			<td id="jLastStatusText<?php echo $jobItem['jID']?>" class="jLastStatusText"><?php echo $jobItem['jLastStatusText']?></td>
			<td class="center">
				<?php  if(!$jobItem['jNotUninstallable']){ ?>
				<form method="post" action="<?php echo $this->url('/dashboard/system/jobs', 'uninstall')?>" onsubmit="return Jobs.confirmUninstall();">
					<input name="jID" type="hidden" value="<?php echo $jobItem['jID'] ?>" />
					<input name="Remove" type="Submit" value="<?php echo t('Remove')?>" />
				</form>
				<?php  } ?>
			</td>
		</tr>	
		<?php  } ?>

	</table>
	</div>
	
<?php  } ?>


<?php  if (count($availableJobs) > 0) { ?>
	

	<br/>
	
	<h2><?php echo t('Jobs Available for Installation')?></h2>
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table class="grid-list" width="100%" cellspacing="1" cellpadding="0">
		<tr> 
			<td class="subheader"><?php echo t('Name')?></td>
			<td class="subheader"><?php echo t('Description')?></td> 
			<td class="subheader" width="10%">&nbsp;</td>
		</tr>
		
		<?php  foreach( $availableJobs as $availableJobName=>$availableJobObj ){ ?>
		<tr> 
			<td><?php echo $availableJobObj->getJobName() ?></td>
			<td><?php echo $availableJobObj->getJobDescription() ?></td> 
			<td class="center">
				<?php  if(!$availableJobObj->invalid){ ?>
				<form method="post" action="<?php echo $this->url('/dashboard/system/jobs', 'install')?>">
					<input name="jHandle" type="hidden" value="<?php echo $availableJobObj->jHandle ?>" />
					<input name="Install" type="Submit" value="Install" />
				</form>
				<?php  }else echo '&nbsp;'; ?>
			</td>
		</tr>	
		<?php  } ?>
	
	</table>
	</div>
	
<?php  } ?>

<br/><br/>
<?php echo t('If you wish to run these jobs in the background, automate access to the following URL:')?>
<br/><br/>
<code>
<?php echo BASE_URL . $this->url('/tools/required/jobs?auth=' . $auth)?>
</div>