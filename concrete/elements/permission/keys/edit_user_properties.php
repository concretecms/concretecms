<?php defined('C5_EXECUTE') or die('Access Denied.');

$included = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_INCLUDE);
$excluded = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_EXCLUDE);
$attributes = UserAttributeKey::getList();

$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
$form = $app->make('helper/form');
?>

<?php if (count($included) > 0 || count($excluded) > 0) { ?>
    <?php if (count($included) > 0) { ?>
        <h3 class="mt-2"><?=t('Who can edit what?')?></h3>

        <?php foreach ($included as $assignment) {
            $entity = $assignment->getAccessEntityObject();
        ?>
            <div class="clearfix">
            	<label class="col-form-label"><?=$entity->getAccessEntityLabel()?></label>
            	<div class="input mb-4">
            	    <?=$form->select('propertiesIncluded[' . $entity->getAccessEntityID() . ']', array('A' => t('All Attributes'), 'C' => t('Custom')), $assignment->getAttributesAllowedPermission())?>
                    <br>
                	<div class="ml-2 attribute-list inputs-list" <?php if ($assignment->getAttributesAllowedPermission() != 'C') { ?>style="display: none"<?php } ?>>
            		<?php
                    $index = 1;
                    foreach ($attributes as $ak) { ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="akIDInclude[<?=$entity->getAccessEntityID()?>][]_<?=$index?>" name="akIDInclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ak->getAttributeKeyID()?>" <?php
                                if ($assignment->getAttributesAllowedPermission() == 'A' || in_array($ak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) { ?> checked="checked" <?php } ?>>
                            <label class="form-check-label" for="akIDInclude[<?=$entity->getAccessEntityID()?>][]_<?=$index?>">
                                <?=$ak->getAttributeKeyDisplayName()?>
                            </label>
                        </div>
            		<?php
                        $index++;
                    }
                    ?>
                	</div>

                	<div class="ml-2 inputs-list">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="allowEditUName[<?=$entity->getAccessEntityID()?>] name="allowEditUName[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditUserName()) { ?>checked="checked" <?php } ?>>
                            <label class="form-check-label" for="allowEditUName[<?=$entity->getAccessEntityID()?>]">
                                <?=t('Username')?>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="allowEditUEmail[<?=$entity->getAccessEntityID()?>]" name="allowEditUEmail[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditEmail()) { ?>checked="checked" <?php } ?>>
                            <label class="form-check-label" for="allowEditUEmail[<?=$entity->getAccessEntityID()?>]">
                                <?=t('Email Address')?>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="allowEditUPassword[<?=$entity->getAccessEntityID()?>]" name="allowEditUPassword[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditPassword()) { ?>checked="checked" <?php } ?>>
                            <label class="form-check-label" for="allowEditUPassword[<?=$entity->getAccessEntityID()?>]">
                                <?=t('Password')?>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="allowEditUAvatar[<?=$entity->getAccessEntityID()?>]" name="allowEditUAvatar[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditAvatar()) { ?>checked="checked" <?php } ?>>
                            <label class="form-check-label" for="allowEditUAvatar[<?=$entity->getAccessEntityID()?>]">
                                <?=t('Avatar')?>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="allowEditUTimezone[<?=$entity->getAccessEntityID()?>]" name="allowEditUTimezone[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditTimezone()) { ?>checked="checked" <?php } ?>>
                            <label class="form-check-label" for="allowEditUTimezone[<?=$entity->getAccessEntityID()?>]">
                                <?=t('Timezone')?>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="allowEditUDefaultLanguage[<?=$entity->getAccessEntityID()?>]" name="allowEditUDefaultLanguage[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditDefaultLanguage()) { ?>checked="checked" <?php } ?>>
                            <label class="form-check-label" for="allowEditUDefaultLanguage[<?=$entity->getAccessEntityID()?>]">
                                <?=t('Default Language')?>
                            </label>
                        </div>
                	</div>
            	</div>
            </div>
        <?php
        }
    }
    ?>

    <?php if (count($excluded) > 0) { ?>
        <h3 class="mt-2"><?=t('Who can\'t edit what?')?></h3>
        <?php foreach ($excluded as $assignment) {
            $entity = $assignment->getAccessEntityObject();
        ?>
            <div class="clearfix">
            	<label class="col-form-label"><?=$entity->getAccessEntityLabel()?></label>
            	<div class="input mb-4">
                	<?=$form->select('propertiesExcluded[' . $entity->getAccessEntityID() . ']', array('N' => t('No Attributes'), 'C' => t('Custom')), $assignment->getAttributesAllowedPermission())?>
                    <br>
                	<div class="ml-2 attribute-list inputs-list" <?php if ($assignment->getAttributesAllowedPermission() != 'C') { ?>style="display: none"<?php } ?>>
                		<?php
                        $index=1;
                        foreach ($attributes as $ak) { ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="akIDExclude[<?=$entity->getAccessEntityID()?>][]" id="akIDExclude[<?=$entity->getAccessEntityID()?>][]_<?=$index?>" value="<?=$ak->getAttributeKeyID()?>" <?php
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

                	<div class="ml-2 inputs-list">
                		<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="allowEditUNameExcluded[<?=$entity->getAccessEntityID()?>]" name="allowEditUNameExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditUserName()) { ?>checked="checked" <?php } ?>>
                            <label class="form-check-label" for="allowEditUNameExcluded[<?=$entity->getAccessEntityID()?>]">
                                <?=t('Username')?>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="allowEditUEmailExcluded[<?=$entity->getAccessEntityID()?>]" name="allowEditUEmailExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditEmail()) { ?>checked="checked" <?php } ?>>
                            <label class="form-check-label" for="allowEditUEmailExcluded[<?=$entity->getAccessEntityID()?>]">
                                <?=t('Email Address')?>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="allowEditUPasswordExcluded[<?=$entity->getAccessEntityID()?>]" name="allowEditUPasswordExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditPassword()) { ?>checked="checked" <?php } ?>>
                            <label class="form-check-label" for="allowEditUPasswordExcluded[<?=$entity->getAccessEntityID()?>]">
                                <?=t('Password')?>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="allowEditUAvatarExcluded[<?=$entity->getAccessEntityID()?>]" name="allowEditUAvatarExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditAvatar()) { ?>checked="checked" <?php } ?>>
                            <label class="form-check-label" for="allowEditUAvatarExcluded[<?=$entity->getAccessEntityID()?>]">
                                <?=t('Avatar')?>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="allowEditUTimezoneExcluded[<?=$entity->getAccessEntityID()?>]" name="allowEditUTimezoneExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditTimezone()) { ?>checked="checked" <?php } ?>>
                            <label class="form-check-label" for="allowEditUTimezoneExcluded[<?=$entity->getAccessEntityID()?>]">
                                <?=t('Timezone')?>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="allowEditUDefaultLanguageExcluded[<?=$entity->getAccessEntityID()?>]" name="allowEditUDefaultLanguageExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditDefaultLanguage()) { ?>checked="checked" <?php } ?>>
                            <label class="form-check-label" for="allowEditUDefaultLanguageExcluded[<?=$entity->getAccessEntityID()?>]">
                                <?=t('Default Language')?>
                            </label>
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
			$(this).parent().find('div.attribute-list').show();
		} else {
			$(this).parent().find('div.attribute-list').hide();
		}
	});
});
</script>
