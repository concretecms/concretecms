<?php
/**
 *Developer: Ben Ali Faker
 *ProjectManager/developer
 *Date 19/05/18
 *Time 13:37
 **/
$request=Core::make(\Concrete\Core\Http\Request::class);
    $uakProfileDisplay = $request->get('uakProfileDisplay', 0);
    $uakProfileEdit = $request->get('uakProfileEdit', 0);
    $uakProfileEditRequired = $request->get('uakProfileEditRequired', 0);
    $uakRegisterEdit = $request->get('uakRegisterEdit', 0);
    $uakRegisterEditRequired = $request->get('uakRegisterEditRequired', 0);
    $uakMemberListDisplay = $request->get('uakMemberListDisplay', 0);
    $gID=$request->get('gID', null);
    if (empty($gID)) {
        throw new Exception("We can't configure attribute having empty associated group");
    }
$form = Core::make("helper/form"); ?>
<div class="ccm-ui">
<fieldset id="pop-up-key-configuration-for-group-<?= $gID;?>">
    <legend><?=t('User Attribute Options')?></legend>
    <div class="form-group">
    <label class="control-label"><?=t('Public Display')?></label>
        <div class="checkbox">
            <label class="checkbox"><?=$form->checkbox("gIDS[$gID][uakProfileDisplay]", 1, $uakProfileDisplay)?> <?=t('Displayed in Public Profile.');?></label>
        </div>
        <div class="checkbox">
            <label class="checkbox"><?=$form->checkbox("gIDS[$gID][uakMemberListDisplay]", 1, $uakMemberListDisplay)?> <?=t('Displayed on Member List.');?></label>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label"><?=t('Edit Mode')?></label>
        <div class="checkbox">
            <label class="checkbox"><?=$form->checkbox("gIDS[$gID][uakProfileEdit]", 1, $uakProfileEdit)?> <?=t('Editable in Profile.');?></label>
        </div>
        <div class="checkbox">
            <label class="checkbox"><?=$form->checkbox("gIDS[$gID][uakProfileEditRequired]", 1, $uakProfileEditRequired)?> <?=t('Editable and Required in Profile.');?></label>
        </div>
    </div>


    <div class="form-group">
        <label class="control-label"><?=t('Registration')?></label>
        <div class="checkbox">
            <label class="checkbox"><?=$form->checkbox("gIDS[$gID][uakRegisterEdit]", 1, $uakRegisterEdit)?> <?=t('Show on Registration Form.');?></label>
        </div>
        <div class="checkbox">
            <label class="checkbox"><?=$form->checkbox("gIDS[$gID][uakRegisterEditRequired]", 1, $uakRegisterEditRequired)?> <?=t('Require on Registration Form.');?></label>
        </div>
    </div>
</fieldset>

<script type="text/javascript">
$(function() {
	$("#pop-up-key-configuration-for-group-<?= $gID;?> input[name='gIDS[<?php echo $gID;?>][uakProfileEdit]']").click(function() {
		if ($(this).prop('checked')) {
			$("#pop-up-key-configuration-for-group-<?= $gID;?> input[name='gIDS[<?php echo $gID;?>][uakProfileEditRequired]']").attr('disabled', false);
		} else {
			$("#pop-up-key-configuration-for-group-<?= $gID;?> input[name='gIDS[<?php echo $gID;?>][uakProfileEditRequired]']").attr('checked', false);
			$("#pop-up-key-configuration-for-group-<?= $gID;?> input[name='gIDS[<?php echo $gID;?>][uakProfileEditRequired]']").attr('disabled', true);
		}
	});

	$("#pop-up-key-configuration-for-group-<?= $gID;?> input[name='gIDS[<?php echo $gID;?>][uakRegisterEdit]']").click(function() {
		if ($(this).prop('checked')) {
			$("#pop-up-key-configuration-for-group-<?= $gID;?> input[name='gIDS[<?php echo $gID;?>][uakRegisterEditRequired]']").attr('disabled', false);
		} else {
			$("#pop-up-key-configuration-for-group-<?= $gID;?> input[name='gIDS[<?php echo $gID;?>][uakRegisterEditRequired]']").attr('checked', false);
			$("#pop-up-key-configuration-for-group-<?= $gID;?> input[name='gIDS[<?php echo $gID;?>][uakRegisterEditRequired]']").attr('disabled', true);
		}
	});


	if ($("#pop-up-key-configuration-for-group-<?= $gID;?> input[name='gIDS[<?php echo $gID;?>][uakProfileEdit]']").prop('checked')) {
		$("#pop-up-key-configuration-for-group-<?= $gID;?> input[name='gIDS[<?php echo $gID;?>][uakProfileEditRequired]']").attr('disabled', false);
	} else {
        $("#pop-up-key-configuration-for-group-<?= $gID;?> input[name='gIDS[<?php echo $gID;?>][uakProfileEditRequired]']").attr('checked', false);
        $("#pop-up-key-configuration-for-group-<?= $gID;?> input[name='gIDS[<?php echo $gID;?>][uakProfileEditRequired]']").attr('disabled', true);
	}

	if ($("#pop-up-key-configuration-for-group-<?= $gID;?> input[name='gIDS[<?php echo $gID;?>][uakRegisterEdit]']").prop('checked')) {
		$("#pop-up-key-configuration-for-group-<?= $gID;?> input[name='gIDS[<?php echo $gID;?>][uakRegisterEditRequired]']").attr('disabled', false);
	} else {
    $("#pop-up-key-configuration-for-group-<?= $gID;?> input[name='gIDS[<?php echo $gID;?>][uakRegisterEditRequired]']").attr('checked', false);
    $("#pop-up-key-configuration-for-group-<?= $gID;?> input[name='gIDS[<?php echo $gID;?>][uakRegisterEditRequired]']").attr('disabled', true);
	}

});
</script>
    <div class="dialog-buttons">
        <?php $ih = Core::make('helper/concrete/ui'); ?>
        <?=$ih->buttonJs(t('Close'), 'jQuery.fn.dialog.closeTop()', 'left', 'btn')?>
        <?=$ih->buttonJs(t('Save'), "updateKeyConfigurationPerGroup()", 'right', 'btn btn-primary');?>
    </div>
    <script type="text/javascript">
        function updateKeyConfigurationPerGroup()
        {
            if($(".key-configuration-for-group-<?= $gID;?>-container").size()>0)
            {
                $(".key-configuration-for-group-<?= $gID;?>-container").empty();
            }
            else {
                 $($("<div class='key-configuration-for-group-<?= $gID;?>-container'/>").insertAfter(".key-per-user-groups-container"))
            }
            $("#pop-up-key-configuration-for-group-<?= $gID;?> :checkbox").each(function(index,element)
            {
                $(".key-configuration-for-group-<?= $gID;?>-container").append($(element).hide());
            });
            jQuery.fn.dialog.closeTop();
        }
    </script>
</div>

