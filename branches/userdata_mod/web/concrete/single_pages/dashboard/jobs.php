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
			alert('Please first check jobs you want to run.');
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
			url: CCM_TOOLS_PATH+'/jobs.php?auth=<?=$auth?>&jID='+jID,
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
		$.ajax({  url: CCM_TOOLS_PATH+'/tools/required/jobs.php?auth=<?=$auth?>&jID='+cb.value+'&jStatus='+jStatus  });
	},
	
	confirmUninstall:function(){
		if( confirm('Are you sure you want to uninstall this job?') )
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

<style>
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

<h1><span>Scheduled Jobs</span></h1>

<div class="ccm-dashboard-inner">


<? if(  $jobListRS->numRows() == 0 ){ ?>
	
	<div style="margin:16px 0px"><strong>You currently have no jobs installed.</strong></div>

<? }else{

$ih = Loader::helper('concrete/interface');

?>

	<?
		$b1 = $ih->button_js('Run Checked', 'Jobs.runChecked');
		//print $ih->buttons($b1);
		print '<div class="ccm-buttons"><a onclick="Jobs.runChecked()" href="javascript:void(0)"><div class="ccm-button"><span>Run Checked</span></div></a></div>';
	?>

	<h2 style="padding-bottom:8px; padding-top:16px">Installed Jobs</h2>
	
	<div class="ccm-spacer">&nbsp;</div>
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table class="grid-list" width="100%" cellspacing="1" cellpadding="0" border="0">
	
		<tr>
			<td class="subheader center" >
				<a onclick="Jobs.selectAll()">All</a> | <a onclick="Jobs.selectNone()">None</a>
			</td>
			<td class="subheader">Name</td>
			<td class="subheader">Description</td>
			<td class="subheader">Last Run</td>
			<td class="subheader">Results of Last Run</td>
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
			<td><?=$jobItem['jName']?></td>
			<td><?=$jobItem['jDescription']?></td>
			<td id="jDateLastRun<?=$jobItem['jID']?>">
				<?
				if($jobItem['jStatus']=='RUNNING'){
					$runtime=date('H:i:s A', strtotime($jobItem['jDateLastRun']) );
					echo "<strong>Currently Running </strong>(Since $runtime)";					
				}elseif(substr($jobItem['jDateLastRun'],0,4)=='0000'){
					echo 'Never';
				}else{
					$runtime=date('n/j/y \a\t g:i A', strtotime($jobItem['jDateLastRun']) );
					echo $runtime;
				}
				?>
			</td>
			<td id="jLastStatusText<?=$jobItem['jID']?>" class="jLastStatusText"><?=$jobItem['jLastStatusText']?></td>
			<td class="center">
				<? if(!$jobItem['jNotUninstallable']){ ?>
				<form method="post" action="<?=$this->url('/dashboard/jobs', 'uninstall')?>" onsubmit="return Jobs.confirmUninstall();">
					<input name="jID" type="hidden" value="<?=$jobItem['jID'] ?>" />
					<input name="Remove" type="Submit" value="Remove" />
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
	
	<h2>Jobs Available for Installation</h2>
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table class="grid-list" width="100%" cellspacing="1" cellpadding="0">
		<tr> 
			<td class="subheader">Name</td>
			<td class="subheader">Description</td> 
			<td class="subheader" width="10%">&nbsp;</td>
		</tr>
		
		<? foreach( $availableJobs as $availableJobName=>$availableJobObj ){ ?>
		<tr> 
			<td><?=$availableJobObj->jName ?></td>
			<td><?=$availableJobObj->jDescription ?></td> 
			<td class="center">
				<? if(!$availableJobObj->invalid){ ?>
				<form method="post" action="<?=$this->url('/dashboard/jobs', 'install')?>">
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
If you wish to run these jobs in the background, automate access to the following URL:
<br/><br/>
<code>
<?=BASE_URL . $this->url('/tools/required/jobs.php?auth=' . $auth)?>
</div>