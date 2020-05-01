<?php
use Concrete\Core\Entity\Attribute\Key\Settings\UserGroupSettings;

?>
<fieldset>
    <legend><?php echo t('User/Group Options')?></legend>

    <div class="form-group">

        <?php echo $form->label('akDisplayAllGroups', t('Groups to Display'))?>

        <div class="radio"><label>
            <?php echo $form->radio('akDisplayGroupsBeneathSpecificParent', 0, $akDisplayGroupsBeneathSpecificParent)?> <?=t('Display all groups in the list.')?>
        </label></div>

        <div class="radio"><label>
            <?php echo $form->radio('akDisplayGroupsBeneathSpecificParent', 1, $akDisplayGroupsBeneathSpecificParent)?> <?=t('Display groups beneath a specific parent.')?>
        </label></div>
    </div>

    <div class="form-group" data-select="parent-group">
        <?php
        echo $groupSelector->selectGroupWithTree('akDisplayGroupsBeneathParentID', $akDisplayGroupsBeneathParentID);
        ?>
    </div>

    <div class="form-group">

        <?php echo $form->label('akMembersOnly', t('Group Selection'))?>

        <div class="radio"><label>
                <?php echo $form->radio('akGroupSelectionMethodType', 'all', $akGroupSelectionMethodType)?> <?=t('Allow user to select any group in the list.')?>
            </label></div>

        <div class="radio"><label>
                <?php echo $form->radio('akGroupSelectionMethodType', 'custom', $akGroupSelectionMethodType)?> <?=t('Custom selection.')?>
            </label></div>

        <div class="form-group" data-select="select-groups-custom">
            <label class="control-label"><?=t('Populate group list with:')?></label>
            
            <div class="checkbox"><label>
                    <?php echo $form->checkbox('akGroupSelectionMethod[]', UserGroupSettings::GROUP_SELECTION_METHOD_IN_GROUP, $akGroupSelectionMethodInGroup)?> <?=t('A list of groups that the user is in.')?>
                </label></div>

            <div class="checkbox"><label>
                    <?php echo $form->checkbox('akGroupSelectionMethod[]', UserGroupSettings::GROUP_SELECTION_METHOD_PERMISSIONS, $akGroupSelectionMethodPermissions)?> <?=t('A list of groups the user is allowed to assign.')?>
                </label></div>

        </div>
    </div>

</fieldset>

<script type="text/javascript">
$(function() {
    $('input[name=akDisplayGroupsBeneathSpecificParent]').on('change', function() {
        var display = $('input[name=akDisplayGroupsBeneathSpecificParent]:checked').val();
        if (display > 0) {
            $('div[data-select=parent-group]').show();
        } else {
            $('div[data-select=parent-group]').hide();
        }
    }).trigger("change");

    $('input[name=akGroupSelectionMethodType]').on('change', function() {
        var methodType = $('input[name=akGroupSelectionMethodType]:checked').val();
        if (methodType == 'custom') {
            $('div[data-select=select-groups-custom]').show();
        } else {
            $('div[data-select=select-groups-custom]').hide();
        }
    }).trigger("change");

});
</script>
