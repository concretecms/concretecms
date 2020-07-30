<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Block\BlockType\BlockTypeList;
use Concrete\Core\Support\Facade\Application;

/**
 * @var Concrete\Core\Permission\Access\Access $permissionAccess
 */

$app = Application::getFacadeApplication();
$form = $app->make('helper/form');

$blockTypes = $app->make(BlockTypeList::class)->get();
$included = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_INCLUDE);
$excluded = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_EXCLUDE);

if (count($included) === 0 && count($excluded) === 0) {
?>
    <p><?= ('No users or groups selected.') ?></p>
<?php
    return;
}

if (count($included) > 0) {
    ?>
    <h4><?=t('Who can add what?')?></h4>

    <?php foreach ($included as $assignment) {
        $entity = $assignment->getAccessEntityObject();
        ?>

        <div class="form-group">
            <?= $form->label("blockTypesIncluded[{$entity->getAccessEntityID()}]", $entity->getAccessEntityLabel()) ?>
            <?= $form->select("blockTypesIncluded[{$entity->getAccessEntityID()}]", ['A' => t('All Block Types'), 'C' => t('Custom')], $assignment->getBlockTypesAllowedPermission()) ?>
            <div class="inputs-list mt-4" <?php if ($assignment->getBlockTypesAllowedPermission() != 'C') { ?>style="display: none"<?php } ?>>
                <?php foreach ($blockTypes as $index => $bt) { ?>
                    <div class="form-check">
                        <?= $form->checkbox("btIDInclude[{$entity->getAccessEntityID()}][]", $bt->getBlockTypeID(), in_array($bt->getBlockTypeID(), $assignment->getBlockTypesAllowedArray()), ['id' => "btIDInclude{$entity->getAccessEntityID()}_{$index}"]) ?>
                        <?= $form->label("btIDInclude{$entity->getAccessEntityID()}_{$index}", t($bt->getBlockTypeName()), ['class' => 'form-check-label']) ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php
    }
}

if (count($excluded) > 0) {
    ?>
    <h3><?=t('Who can\'t add what?')?></h3>

    <?php foreach ($excluded as $assignment) {
        $entity = $assignment->getAccessEntityObject();
        ?>

        <div class="form-group">
            <?= $form->label("blockTypesExcluded[{$entity->getAccessEntityID()}]", $entity->getAccessEntityLabel()) ?>
            <?= $form->select("blockTypesExcluded[{$entity->getAccessEntityID()}]", ['N' => t('No Block Types'), 'C' => t('Custom')], $assignment->getBlockTypesAllowedPermission()) ?>
            <div class="inputs-list mt-4" <?php if ($assignment->getBlockTypesAllowedPermission() != 'C') { ?>style="display: none"<?php } ?>>
                <?php foreach ($blockTypes as $index => $bt) { ?>
                    <div class="form-check">
                        <?= $form->checkbox("btIDExclude[{$entity->getAccessEntityID()}][]", $bt->getBlockTypeID(), in_array($bt->getBlockTypeID(), $assignment->getBlockTypesAllowedArray()), ['id' => "btIDExclude{$entity->getAccessEntityID()}_{$index}"]) ?>
                        <?= $form->label("btIDExclude{$entity->getAccessEntityID()}_{$index}", t($bt->getBlockTypeName()), ['class' => 'form-check-label']) ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php
    }
}
?>

<script type="text/javascript">
$(function() {
	$("#ccm-tab-content-custom-options select").change(function() {
		if ($(this).val() == 'C') {
			$(this).parent().find('div.inputs-list').show();
		} else {
			$(this).parent().find('div.inputs-list').hide();
		}
	});
});
</script>
