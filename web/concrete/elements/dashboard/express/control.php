<?php
defined('C5_EXECUTE') or die("Access Denied.");

$final_label = $control->getDisplayLabel();
$original_label = $control->getControlLabel();
$type_name = $control->getControlType()->getDisplayName();

?>

<tr class="ccm-item-set-item" data-field-set-control="<?=$control->getID()?>">
	<td style="width: 20%; white-space: nowrap"><?= $final_label ?></td>
	<td style="width: 100%;">
		<span class="text-muted"><?= $type_name ?></span>
		<?php

		if ($final_label != $original_label) { ?>
			<span class="text-muted">(<?= $original_label ?>)</span>
		<?php } ?>
	</td>
	<td>
		<ul class="ccm-item-set-controls">
			<li><a href="#" data-command="move-control" style="cursor: move"><i class="fa fa-arrows"></i></a></li>
			<li><a href="#" data-dialog="update-control-<?=$control->getId()?>" data-dialog-title="<?=t('Update Control')?>"><i class="fa fa-pencil"></i></a></li>
			<li><a href="#" data-dialog="delete-control-<?=$control->getId()?>" data-dialog-title="<?=t('Delete Control')?>"><i class="fa fa-trash-o"></i></a></li>
		</ul>
	</td>
</tr>

