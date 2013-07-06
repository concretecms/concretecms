<?php defined('C5_EXECUTE') or die("Access Denied.");
$dh = Loader::helper("Date");
?>
<div class="grid_4 alpha omega" id="main-content-sidebar-archives">
	<h3><?php echo $title ?></h3>
	<?php
	if ($firstPost) { 
		$startDt = new DateTime();
		$firstPost = new DateTime($firstPost->format('m/01/Y'));
		$first = date_diff($startDt,$firstPost);
		$first = $first->m;
		
		if($first > $numMonths) {
			$first = $numMonths;
		}	
		
		$startDt->modify('-'.$first.' months');
		$workingDt = $startDt;
		$year = $workingDt->format('Y');
		?>
		<h4><?php echo $year;?></h4>
		<ul>
			<?php 
			$i=0;
			while(true) {
				if($workingDt->format('Y') > $year) {
					$year = $workingDt->format('Y');
					?></ul><h4><?=$year?></h4><ul><?php
				}
				?>
				<li>
				<?php if($target instanceof Page) { ?>
						<a href="<?php echo $navigation->getLinkToCollection($target)."?year=".$workingDt->format('Y'). "&month=".$workingDt->format('m') ?>" <?php echo ($workingDt->format('m-Y') == $_REQUEST['month']?'class="selected"':'')?>><?php echo $dh->dateTimeFormatLocal($workingDt,'M') ?></a>
				<?php } else { ?>
						<?php echo $dh->dateTimeFormatLocal($workingDt,'M') ?>
				<?php } ?>
				</li>
				<?php
				if($i >= $first) { break; }
				$workingDt->modify('+1 month');
				$i++;
			} ?>
		</ul>
<? } ?>
</div>
