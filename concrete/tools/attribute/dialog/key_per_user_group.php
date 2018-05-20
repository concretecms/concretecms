<?php
/**
 *Developer: Ben Ali Faker
 *ProjectManager/developer
 *Date 18/05/18
 *Time 12:55
 **/
$token=Core::make('token');
$groupsJSON=(Core::make(\Concrete\Core\Http\Request::class))->get("groups", json_encode([]));
?>
<div class="ccm-ui">
<div class="row">
    <div  class="col-md-10"><h4><?= t('Groups') ?></h4></div>
    <div  class="col-md-2">
         <a class="btn  btn-default btn-xs" data-button="assign-groups" dialog-width="640"
                           dialog-height="480" dialog-modal="true"
                           href="<?= URL::to('/ccm/system/dialogs/group/search') ?>"
                           dialog-title="<?= t('Add Groups') ?>"><?= t('Add') ?></a>
</div>
</div>
<table class="table" style="margin-top:7px">
<tbody class="tbody-groups-container-list">
</tbody>
</table>

    <script type="text/template" data-template="user-add-groups">
        <% _.each(groups, function(group) { %>
        <tr data-group-id="<%=group.gID%>" data-group-name=" <%=group.gName%>">
            <td class="one-group"><%=group.gName%></td>
            <td>
                <a  href="javascript:void(0)" class="icon-link pull-right" onclick="deleteGroupFromList(this)" style="margin-left: 10px" ><i class="fa fa-trash-o"></i></a>
                <a  class="edit-key-configuration-assciated-to-group icon-link pull-right"   href="javascript:void(0)"
                   dialog-title="<?= t('User Key Configuration for');?> <%=group.gName %>"><i class="fa fa-edit "></i></a>
            </td>
        </tr>
        <% }); %>
    </script>


<script type="text/javascript">
    $('a[data-button=assign-groups]').dialog();
    $(function () {
        var _addGroupsTemplate = _.template($('script[data-template=user-add-groups]').html());
        $('.tbody-groups-container-list').append(
            _addGroupsTemplate({'groups': <?=$groupsJSON?>}));
        $("body").undelegate("a.edit-key-configuration-assciated-to-group","click").
        delegate("a.edit-key-configuration-assciated-to-group","click",function(e)
        {
            var gID=  $(this).parents("tr").data('group-id');
            var checked_configuration=[];
            $(".key-configuration-for-group-"+gID+"-container :checkbox:checked").each(function(index,element)
            {
                checked_configuration.push($(element).attr("name").match(/.*\[(.*)\]$/)[1]+"=1");
            });
            jQuery.fn.dialog.open({
                title: $(this).attr('dialog-title'),
                href: "<?php echo URL::to('/tools/required/attribute/dialog/key_per_user_group_configuration')?>?gID="+gID+"&"+checked_configuration.join("&"),
                modal: true,
                width: 500,
                height: 380,
            });
        });
        ConcreteEvent.unsubscribe('SelectGroup');
        ConcreteEvent.subscribe('SelectGroup', function (e, data) {
            if($("tr[data-group-id='"+data.gID+"']").size()===0)
            {
            $('.tbody-groups-container-list').append(_addGroupsTemplate({'groups': {data}}));
            }
            jQuery.fn.dialog.closeTop();
        });
    });
    /**
     * Method that update associated group list in user keu edit Interface
     */
    function updateGroups()
   {
       $(".key-per-user-groups-container").empty();
       groupsJSON=[];
       console.log($(".tbody-groups-container-list tr"));
       $(".tbody-groups-container-list tr").each(function()
       {
          groupsJSON.push({"gID":$(this).data('group-id'),"gName":$(this).data('group-name')});

       });
       var _keyPerUserGroupTemplate = _.template($("script[data-template='key-per-user-groups'").html());
       $(".key-per-user-groups-container").append(_keyPerUserGroupTemplate({"groupsJson":groupsJSON}));
       jQuery.fn.dialog.closeTop()
   }

   function deleteGroupFromList(link)
   {
      $(link).parents('tr').remove();
   }
</script>

    <div class="dialog-buttons">
        <?php $ih = Core::make('helper/concrete/ui'); ?>
        <?=$ih->buttonJs(t('Close'), 'jQuery.fn.dialog.closeTop()', 'left', 'btn')?>
        <?=$ih->buttonJs(t('Save'), "updateGroups()", 'right', 'btn btn-primary');?>
    </div>
</div>
<?php
