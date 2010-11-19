<?php 
	defined('C5_EXECUTE') or die("Access Denied.");
	// basically a stub that includes some other files
	$u = new User();
	$uID=$u->getUserID();
?>
<style type="text/css">
#surveyResults .surveySwatch {
	background:#999999 none repeat scroll 0%;
	border:1px solid #666666;
	float:left;
	font-size:1px;
	height:10px;
	line-height:1px;
	margin:3px 4px 0px 0px;
	width:10px;
}
</style>
<div class="poll">
<?php   
if ($controller->hasVoted()) { ?>
		<h3><?php echo t("You've voted on this survey.")?></h3>
		<br/>	
	<div style="">		
		<?php  
		//available chart colors are duplicated in content/surveys.php
		$availableChartColors=array('00CCdd','cc3333','330099','FF6600','9966FF','dd7700','66DD00','6699FF','FFFF33','FFCC33','00CCdd','cc3333','330099','FF6600','9966FF','dd7700','66DD00','6699FF','FFFF33','FFCC33');
		$options = $controller->getPollOptions();
		$optionNames=array();
		$optionResults=array();
		$graphColors=array();
		$i = 1;
		$totalVotes=0;
		foreach($options as $opt) { 
			$optionNamesAbbrev[] = $i;
			$optionResults[]=$opt->getResults();
			$i++;
			$graphColors[]=array_pop($availableChartColors);
			$totalVotes+=intval($opt->getResults());
		}
		?>
		
		<strong><?php echo t("Question")?>: <?php echo $controller->getQuestion()?></strong>
		
		<div id="surveyResults" style=" <?php echo (!$_GET['dontGraphPoll'] && $totalVotes>0)?'float:left; width:45%':''?>">
			<table style="width:98%">
			<?php 	$i = 1; 
				foreach($options as $opt) { ?>
					<tr>
						<td width="55px" class="note" style="white-space:nowrap">
						<div class="surveySwatch" style="background:#<?php echo $graphColors[$i-1]?>"></div>&nbsp;<?php echo ($totalVotes>0)?round($opt->getResults() / $totalVotes * 100):0 ?>% 
						</td>
						<td>
						<strong><?php echo $opt->getOptionName() ?></strong>
						</td>
					</tr>
					<?php  $i++; ?>
			<?php  } ?>
			</table>
			<div class="note" style="margin-top:8px"><?php echo intval($totalVotes)?> <?php echo (intval($totalVotes)==1)?t('Vote'):t('Votes')?></div>
		</div>
		<?php 
		//&chl= join('|',$optionNamesAbbrev) 
		if(count($optionNamesAbbrev) && !$_GET['dontGraphPoll'] && $totalVotes>0){ ?>
		<div >
		<img border="" src="http://chart.apis.google.com/chart?cht=p&chd=t:<?php echo join(',',$optionResults)?>&chs=180x180&chco=<?php echo join(',',$graphColors)?>" />
		</div>
		<?php  } ?>	
		<div class="spacer">&nbsp;</div>	
	</div>
	
	<?php  if($_GET['dontGraphPoll']){ ?>
		<div class="small right" style="margin-top:8px"><a class="arrow" href="<?php echo DIR_REL?>/?cID=<?php echo $b->getBlockCollectionID() ?>"><?php echo t('View Full Results')?></a></div>
	<?php  } ?>
	
	<div class="spacer">&nbsp;</div>

<?php  } else { ?>

	<?php echo $controller->getQuestion()?><br/>
	
	<?php  if(!$controller->requiresRegistration() || intval($uID) > 0) { ?>
	<form method="post" action="<?php echo $this->action('form_save_vote', '#survey-form-'.$controller->bID)?>">
	<?php  } ?>
	
	<?php 	
	$options = $controller->getPollOptions();
	foreach($options as $opt) { ?>
		<input type="radio" name="optionID" style="vertical-align: middle" value="<?php echo $opt->getOptionID()?>" />
		<?php echo $opt->getOptionName() ?><br/>
	<?php  } ?>
	
	<?php  if(!$controller->requiresRegistration() || intval($uID) > 0) { ?>
	<div class="buttons" style="text-align: left !important"><input type="submit" name="submit" value="<?php echo t('Vote')?>" /></div>
	<?php  }else{ ?>
		<div class="faint" style="margin-top:8px"><?php echo t('Please Login to Vote')?></div>
	<?php  } ?>

	<?php  if(!$controller->requiresRegistration() || intval($uID) > 0) { ?>
	</form>
	<?php  } ?>

<?php  } ?>

</div>