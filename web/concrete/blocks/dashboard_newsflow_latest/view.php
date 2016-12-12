<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<? if (is_object($slot)) { ?>
<div>
<?=$slot->getContent()?>
</div>
<? } ?>

<? if ($controller->slot == 'C') { ?>
	<div class="newsflow-paging-next"><a href="javascript:void(0)" onclick="ConcreteNewsflowDialog.loadEdition('<?=$editionID?>')"><i class="fa fa-chevron-right"></i></a></div>
<? } ?>