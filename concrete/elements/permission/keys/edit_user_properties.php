<?php defined('C5_EXECUTE') or die('Access Denied.');

$included = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_INCLUDE);
$excluded = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_EXCLUDE);
$attributes = UserAttributeKey::getList();

$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
$form = $app->make('helper/form');
?>

<?php if (count($included) > 0 || count($excluded) > 0) { ?>
    <?php if (count($included) > 0) { ?>
        <h3><?=t('Who can edit what?')?></h3>

        <?php foreach ($included as $assignment) {
            $entity = $assignment->getAccessEntityObject();
        ?>
            <div class="clearfix">
            	<label class="control-label"><?=$entity->getAccessEntityLabel()?></label>
            	<div class="input">
            	    <?=$form->select('propertiesIncluded[' . $entity->getAccessEntityID() . ']', array('A' => t('All Attributes'), 'C' => t('Custom')), $assignment->getAttributesAllowedPermission())?>
                    <br>
                	<ul class="attribute-list inputs-list" <?php if ($assignment->getAttributesAllowedPermission() != 'C') { ?>style="display: none"<?php } ?>>
            		<?php foreach ($attributes as $ak) { ?>
            			<li>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="akIDInclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ak->getAttributeKeyID()?>" <?php
                                    if ($assignment->getAttributesAllowedPermission() == 'A' || in_array($ak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) { ?> checked="checked" <?php } ?>>
                                    <span><?=$ak->getAttributeKeyDisplayName()?></span>
                                </label>
                            </div>
                        </li>
            		<?php
                    }
                    ?>
                	</ul>

                	<ul class="inputs-list">
                		<li>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="allowEditUName[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditUserName()) { ?>checked="checked" <?php } ?>>
                                    <span><?=t('Username')?></span>
                                </label>
                            </div>
                        </li>
                		<li>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="allowEditUEmail[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditEmail()) { ?>checked="checked" <?php } ?>>
                                    <span><?=t('Email Address')?></span>
                                </label>
                            </div>
                        </li>
                		<li>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="allowEditUPassword[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditPassword()) { ?>checked="checked" <?php } ?>>
                                    <span><?=t('Password')?></span>
                                </label>
                            </div>
                        </li>
                		<li>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="allowEditUAvatar[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditAvatar()) { ?>checked="checked" <?php } ?>>
                                    <span><?=t('Avatar')?></span>
                                </label>
                            </div>
                        </li>
                		<li>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="allowEditUTimezone[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditTimezone()) { ?>checked="checked" <?php } ?>>
                                    <span><?=t('Timezone')?></span>
                                </label>
                            </div>
                        </li>
                		<li>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="allowEditUDefaultLanguage[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditDefaultLanguage()) { ?>checked="checked" <?php } ?>>
                                    <span><?=t('Default Language')?></span>
                                </label>
                            </div>
                        </li>
                	</ul>
            	</div>
            </div>
        <?php
        }
    }
    ?>

    <?php if (count($excluded) > 0) { ?>
        <h3><?=t('Who can\'t edit what?')?></h3>
        <?php foreach ($excluded as $assignment) {
            $entity = $assignment->getAccessEntityObject();
        ?>
            <div class="clearfix">
            	<label class="control-label"><?=$entity->getAccessEntityLabel()?></label>
            	<div class="input">
                	<?=$form->select('propertiesExcluded[' . $entity->getAccessEntityID() . ']', array('N' => t('No Attributes'), 'C' => t('Custom')), $assignment->getAttributesAllowedPermission())?>
                    <br>
                	<ul class="attribute-list inputs-list" <?php if ($assignment->getAttributesAllowedPermission() != 'C') { ?>style="display: none"<?php } ?>>
                		<?php foreach ($attributes as $ak) { ?>
                			<li>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="akIDExclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ak->getAttributeKeyID()?>" <?php
                                        if (in_array($ak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) { ?> checked="checked" <?php } ?>>
                                        <span><?=$ak->getAttributeKeyDisplayName()?></span>
                                    </label>
                                </div>
                            </li>
                		<?php
                        }
                        ?>
                	</ul>

                	<ul class="inputs-list">
                		<li>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="allowEditUNameExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditUserName()) { ?>checked="checked" <?php } ?>>
                                    <span><?=t('Username')?></span>
                                </label>
                            </div>
                        </li>
                		<li>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="allowEditUEmailExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditEmail()) { ?>checked="checked" <?php } ?>>
                                    <span><?=t('Email Address')?></span>
                                </label>
                            </div>
                        </li>
                		<li>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="allowEditUPasswordExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditPassword()) { ?>checked="checked" <?php } ?>>
                                    <span><?=t('Password')?></span>
                                </label>
                            </div>
                        </li>
                		<li>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="allowEditUAvatarExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditAvatar()) { ?>checked="checked" <?php } ?>>
                                    <span><?=t('Avatar')?></span>
                                </label>
                            </div>
                        </li>
                		<li>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="allowEditUTimezoneExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditTimezone()) { ?>checked="checked" <?php } ?>>
                                    <span><?=t('Timezone')?></span>
                                </label>
                            </div>
                        </li>
                		<li>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="allowEditUDefaultLanguageExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditDefaultLanguage()) { ?>checked="checked" <?php } ?>>
                                    <span><?=t('Default Language')?></span>
                                </label>
                            </div>
                        </li>
                	</ul>
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
			$(this).parent().find('ul.attribute-list').show();
		} else {
			$(this).parent().find('ul.attribute-list').hide();
		}
	});
});
</script>
