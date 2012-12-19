<?
	defined('C5_EXECUTE') or die("Access Denied.");
	$a = $b->getBlockAreaObject();
	$c = Page::getCurrentPage();
?>

<div class="ccm-layout-column-wrapper" id="ccm-layout-column-wrapper-<?=$bID?>">

<? for ($i = 1; $i <= $columns; $i++) { ?>

	<div class="ccm-layout-column" id="ccm-layout-column-<?=$i?>">
		<div class="ccm-layout-column-inner">
			<? 
			$as = new SubArea("Column $i", $a);
			$as->display($c);
			?>
		</div>
	</div>

<? } ?>

</div>

Columns <?=$columns?> Spacing <?=$spacing?>