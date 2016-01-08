<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php if (is_object($slot)) { ?>
<div>
<?=$slot->getContent()?>
</div>
<?php } ?>

<?php if ($controller->slot == 'C') { ?>
	<div class="newsflow-paging-next"><a href="javascript:void(0)" onclick="ConcreteNewsflowDialog.loadEdition('<?=$editionID?>')"><i class="fa fa-chevron-right"></i></a></div>
<?php } ?>