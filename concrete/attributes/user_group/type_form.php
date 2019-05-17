<fieldset>
    <legend><?php echo t('User/Group Options')?></legend>

    <div class="form-group">

        <?php echo $form->label( 'akDisplayAllGroups', t('Groups to Display') )?>

        <div class="radio"><label>
            <?php echo $form->radio( 'akDisplayGroupsBeneathSpecificParent' , 0, $akDisplayGroupsBeneathSpecificParent)?> <?=t('Display all groups in the list.')?>
        </label></div>

        <div class="radio"><label>
            <?php echo $form->radio( 'akDisplayGroupsBeneathSpecificParent' , 1, $akDisplayGroupsBeneathSpecificParent)?> <?=t('Display groups beneath a specific parent.')?>
        </label></div>
    </div>

    <div class="form-group" data-select="parent-group">
        <?php
        print $groupSelector->selectGroupWithTree('akDisplayGroupsBeneathParentID', $akDisplayGroupsBeneathParentID);
        ?>
    </div>

    <div class="form-group">

        <?php echo $form->label( 'akMembersOnly', t('Group Selection') )?>

        <div class="radio"><label>
                <?php echo $form->radio( 'akAllowSelectionFromMyGroupsOnly' , 0, $akAllowSelectionFromMyGroupsOnly)?> <?=t('Allow user to select any group in the list.')?>
            </label></div>

        <div class="radio"><label>
                <?php echo $form->radio( 'akAllowSelectionFromMyGroupsOnly' , 1, $akAllowSelectionFromMyGroupsOnly)?> <?=t('User may select only those groups they are in.')?>
            </label></div>
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
});
</script>