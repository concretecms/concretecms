<?php
/**
 *Developer: Ben Ali Faker
 *ProjectManager/developer
 *Date 18/05/18
 *Time 11:14
 *
 *
 *
 * @var \Concrete\Core\Entity\Attribute\Key\UserKey $key
 * @var Group[] $groups
 * @var $request Request
 **/
$request=Core::make(\Concrete\Core\Http\Request::class);
$groups=($key)?$key->getAssociatedGroups():array();
if (count($groups)==0&&$request->request->has("gIDS")&&isset($request->get("gIDS")['ids'])) {
    foreach ($request->request->get("gIDS")['ids'] as $gID) {
        $groups[]=Group::getByID($gID);
    }
}
$groupsJson=[];
$form = Core::make("helper/form");
?>
<div  class="form-group">
    <label class="groups-edit control-label"><?= t('Associated Groups') ?><a onclick="ccm_permissionLaunchDialog(this)" data-akid="<?php echo (!empty($key))?$key->getAttributeKeyID():"null";?>"  dialog-title="<?= t('Associated Groups') ?>" class="edit-groups"href="javascript:void(0)"><i style="padding-left:5px" class="fa fa-edit"></i></a> </label>
    <p>
        <strong>
            <?php echo t("Used by all group if none is selected");?>
        </strong>
    </p>

     <div class="key-per-user-groups-container ui-draggable ui-draggable-handle">
     </div>
    <?php if (sizeof($groups)>0):?>
       <?php foreach ($groups as $group) :?>
          <?php
            $gID=$group->getGroupID();
            $groupsJson[]=['gName'=>tc("GroupName", $group->getGroupName()),"gID"=>$gID];
            $keyConfigurationForGroup=(!empty($key))?$key->getKeyConfigurationForGroup($group):null;
?>
            <div class='key-configuration-for-group-<?= $group->getGroupID();?>-container'>
                <?=$form->checkbox("gIDS[$gID][uakProfileDisplay]", 1, (!empty($keyConfigurationForGroup))?$keyConfigurationForGroup->isAttributeKeyDisplayedOnProfile():0, array('class'=>"hidden"))?>
                <?=$form->checkbox("gIDS[$gID][uakMemberListDisplay]", 1, (!empty($keyConfigurationForGroup))?$keyConfigurationForGroup->isAttributeKeyDisplayedOnMemberList():0, array('class'=>"hidden"))?>
                <?=$form->checkbox("gIDS[$gID][uakProfileEdit]", 1, (!empty($keyConfigurationForGroup))?$keyConfigurationForGroup->isAttributeKeyEditableOnProfile():0, array('class'=>"hidden"))?>
                <?=$form->checkbox("gIDS[$gID][uakProfileEditRequired]", 1, (!empty($keyConfigurationForGroup))?$keyConfigurationForGroup->isAttributeKeyRequiredOnProfile():0, array('class'=>"hidden"))?>
                <?=$form->checkbox("gIDS[$gID][uakRegisterEdit]", 1, (!empty($keyConfigurationForGroup))?$keyConfigurationForGroup->isAttributeKeyEditableOnRegister():0, array('class'=>"hidden"))?>
                <?=$form->checkbox("gIDS[$gID][uakRegisterEditRequired]", 1, (!empty($keyConfigurationForGroup))?$keyConfigurationForGroup->isAttributeKeyRequiredOnRegister():0, array('class'=>"hidden"))?>
            </div>
        <?php endforeach ;?>
    <?php endif ;?>
</div>


<script type="text/template" data-template="key-per-user-groups">
<% _.each(groupsJson,function(group) { %>
<span class="label label-default"><%= group.gName %></span>
<input type="hidden" name="gIDS[ids][]" data-group-name="<%= group.gName %>" value="<%= group.gID %>">
<% }); %>
</script>


<script type="text/javascript">
    var groupsJSON =<?php echo json_encode($groupsJson);?>;
    $(function () {
        var _keyPerUserGroupTemplate = _.template($("script[data-template='key-per-user-groups'").html());
        $(".key-per-user-groups-container").append(_keyPerUserGroupTemplate({"groupsJson":groupsJSON}));
        ccm_permissionLaunchDialog = function (link) {
            groupsJSON=[];
            $("[name='gIDS[ids][]']").each(function (index, element) {
                groupJson = {};
                groupJson["gID"]=$(element).val();
                groupJson["gName"]=$(element).data("group-name");
                groupsJSON.push(groupJson);
            });
            jQuery.fn.dialog.open({
                title: $(link).attr('dialog-title'),
                href: "<?php echo URL::to('/tools/required/attribute/dialog/key_per_user_group');?>?akID=" + $(link).data('akid') + '&groups=' + JSON.stringify(groupsJSON),
                modal: true,
                width: 500,
                height: 380,
            });
        };
    });
</script>