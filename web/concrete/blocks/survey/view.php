<?
	defined('C5_EXECUTE') or die("Access Denied.");
	// basically a stub that includes some other files
	$u = new User();
	$uID=$u->getUserID();
?>

<div class="poll">
<?  
if ($controller->hasVoted()) { ?>
		<h3><?=t("You've voted on this survey.")?></h3>
		<br/>	
	<div style="">		
		<? 
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
		foreach ($optionResults as &$value){
			if($totalVotes) {
				$value=round($value/$totalVotes*100,0);
			}
		}		
		?>

		<div id="surveyQuestion">
			<strong><?=t("Question")?>: <?=$controller->getQuestion()?></strong>
		</div>

		<div id="surveyResults" style=" <?=(!$_GET['dontGraphPoll'] && $totalVotes>0)?'float:left; width:45%':''?>">
			<table style="width:98%">
			<?	$i = 1; 
				foreach($options as $opt) { ?>
					<tr>
						<td width="55px" class="note" style="white-space:nowrap">
						<div class="surveySwatch" style="background:#<?=$graphColors[$i-1]?>"></div>&nbsp;<?=($totalVotes>0)?round($opt->getResults() / $totalVotes * 100):0 ?>% 
						</td>
						<td>
						<strong><?=$opt->getOptionName() ?></strong>
						</td>
					</tr>
					<? $i++; ?>
			<? } ?>
			</table>
			<div class="note" style="margin-top:8px"><?=t2('%d Vote', '%d Votes', intval($totalVotes), intval($totalVotes))?></div>
		</div>
		<?
		//&chl= join('|',$optionNamesAbbrev) 
		if(count($optionNamesAbbrev) && !$_GET['dontGraphPoll'] && $totalVotes>0){ ?>
		<div >
		<img border="" src="//chart.apis.google.com/chart?cht=p&chd=t:<?=join(',',$optionResults)?>&chs=180x180&chco=<?=join(',',$graphColors)?>" alt="<?php echo t('survey results');?>" />
		</div>
		<? } ?>	
		<div class="spacer">&nbsp;</div>	
	</div>
	
	<? if($_GET['dontGraphPoll']){ ?>
		<div class="small right" style="margin-top:8px"><a class="arrow" href="<?=DIR_REL?>/?cID=<?=$b->getBlockCollectionID() ?>"><?=t('View Full Results')?></a></div>
	<? } ?>
	
	<div class="spacer">&nbsp;</div>

<? } else { ?>
	
	<div id="surveyQuestion">
		<?=$controller->getQuestion()?><br/>
	</div>
	
	<? if(!$controller->requiresRegistration() || intval($uID) > 0) { ?>
	<form method="post" action="<?=$this->action('form_save_vote', '#survey-form-'.$controller->bID)?>">
	<? $c = Page::getCurrentPage(); ?>
	<input type="hidden" name="rcID" value="<?=$c->getCollectionID()?>" />
	<? } ?>
	
	<?	
	$options = $controller->getPollOptions();
	foreach($options as $opt) { ?>
		<input type="radio" name="optionID" style="vertical-align: middle" value="<?=$opt->getOptionID()?>" />
		<?=$opt->getOptionName() ?><br/>
	<? } ?>
	
	<? if(!$controller->requiresRegistration() || intval($uID) > 0) { ?>
	<div class="buttons" style="text-align: left !important"><input type="submit" name="submit" value="<?=t('Vote')?>" /></div>
	<? }else{ ?>
		<div class="faint" style="margin-top:8px"><?=t('Please Login to Vote')?></div>
	<? } ?>

	<? if(!$controller->requiresRegistration() || intval($uID) > 0) { ?>
	</form>
	<? } ?>

<? } ?>

</div>
