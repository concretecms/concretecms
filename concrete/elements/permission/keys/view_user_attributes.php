<?php defined('C5_EXECUTE') or die('Access Denied.');

$included = $permissionAccess->getAccessListItems();
$excluded = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_EXCLUDE);
$attribs = UserAttributeKey::getList();
$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
$form = $app->make('helper/form');
?>

<?php if (count($included) > 0 || count($excluded) > 0) { ?>
    <?php if (count($included) > 0) { ?>
        <h3 class="mt-2"><?=t('Who can view what?')?></h3>
        <?php foreach ($included as $assignment) {
            $entity = $assignment->getAccessEntityObject();
        ?>
            <div class="clearfix">
            	<label class="col-form-label"><?=$entity->getAccessEntityLabel()?></label>
            	<div class="input">
                	<?=$form->select('viewAttributesIncluded[' . $entity->getAccessEntityID() . ']', array('A' => t('All Attributes'), 'C' => t('Custom')), $assignment->getAttributesAllowedPermission())?>
                    <br>
                	<div class="inputs-list m-sm-2" <?php if ($assignment->getAttributesAllowedPermission() != 'C') { ?>style="display: none"<?php } ?>>
                		<?php
                       $index = 1;
                        foreach ($attribs as $ak) { ?>

                                <div class="form-check">

                                    <input class="form-check-input" id="akIDInclude[<?=$entity->getAccessEntityID()?>][]_<?=$index?>" type="checkbox" name="akIDInclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ak->getAttributeKeyID()?>" <?php
                                        if (in_array($ak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) { ?> checked="checked" <?php } ?>>
                                    <label class="form-check-label" for="akIDInclude[<?=$entity->getAccessEntityID()?>][]_<?=$index?>">
                                        <?=$ak->getAttributeKeyDisplayName()?>
                                    </label>
                                </div>

                		<?php
                            $index++;
                        }
                        ?>
                	</div>
            	</div>
            </div>
        <?php
        }
    }
    ?>

    <?php if (count($excluded) > 0) { ?>
        <h3 class="mt-2"><?=t('Who can\'t view what?')?></h3>
        <?php foreach ($excluded as $assignment) {
            $entity = $assignment->getAccessEntityObject();
        ?>
            <div class="clearfix">
            	<label class="col-form-label"><?=$entity->getAccessEntityLabel()?></label>
            	<div class="input">
            	<?=$form->select('viewAttributesExcluded[' . $entity->getAccessEntityID() . ']', array('N' => t('No Attributes'), 'C' => t('Custom')), $assignment->getAttributesAllowedPermission())?>
                <br>
                	<div class="inputs-list m-sm-2" <?php if ($assignment->getAttributesAllowedPermission() != 'C') { ?>style="display: none"<?php } ?>>
                		<?php
                        $index= 1;
                        foreach ($attribs as $ak) { ?>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="akIDExclude[<?=$entity->getAccessEntityID()?>][]_<?=$index?>" name="akIDExclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ak->getAttributeKeyID()?>" <?php
                                        if (in_array($ak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) { ?> checked="checked" <?php } ?>>
                                    <label class="form-check-label" for="akIDExclude[<?=$entity->getAccessEntityID()?>][]_<?=$index?>">
                                        <?=$ak->getAttributeKeyDisplayName()?>
                                    </label>
                                </div>
                		<?php
                            $index++;
                        }
                        ?>
                	</div>
            	</div>
            </div>
        <?php
        }
    }
    ?>

<?php
} else { ?>
	<p><?=t('No users or groups selected.')?></p>
<?php
}
?>

<script>
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
