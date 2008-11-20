<?
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('single_page');
Loader::model("advertisement_group", "advertisement");
$ih = Loader::helper('concrete/interface');

$ag = new AdvertisementGroup();
$groups = $ag->Find("TRUE ORDER BY agName");
?>
<h1><span>Advertisement Groups</span></h1>
<div class="ccm-dashboard-inner">
<?
if ($ag_edit) { ?>
<form method="post" action="<?=$this->url('/dashboard/advertisement/groups/','save_group',$ag_edit->agID)?>">
  <!-- <input type="hidden" name="task" value="edit" /> -->
  <table class="entry-form" border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td colspan="1" class="header">Edit/Update Group (<span class="required">*</span> - required field)</td>
    </tr>
    <tr>
      <td class="subheader">Name <span class="required">*</span></td>
    </tr>
    <tr>
      <td><input type="text" name="agName" style="width: 100%" value="<?=$ag_edit->agName?>" /></td>
    </tr>
    <tr>
      <td colspan="1" class="header"><input type="submit" name="update" value="Update Group" />
        <input type="button" name="cancel" value="Cancel" onclick="location.href='<?=DIR_REL?>/dashboard/ad_groups/'" />
    </tr>
  </table>
  <br>
</form>
<? 

} else { ?>
<div class="wrapper">
  <h2><span>Defined Groups</span></h2>
    <? if (count($groups) > 0) { ?>
    <table border="0" cellspacing="1" cellpadding="0" class="grid-list">
      <tr>
        <td colspan="2" class="subheader">Name</td>
      </tr>
      <?
foreach($groups as $ag) { ?>
      <tr>
        <td><?=$ag->agName?></td>
        <td>
        <input type="button" value="Edit" onclick="location.href='<?=$this->url('/dashboard/advertisement/groups/',"load_group",$ag->agID);?>'" />
        <input type="button" value="Delete" onclick="if (confirm('Are you sure you wish to delete this group?')) { location.href='<?=$this->url('/dashboard/advertisement/groups/',"delete_group",$ag->agID);?>' }" /></td>
      </tr>
      <? } ?>
    </table>
    <br/>
    <br/>
    <? } else { ?>
    <br/>
    <strong>No ad groups defined.</strong><br/>
    <br/>
    <? } ?>
  </div>
  <form method="post" action="<?=$this->url('/dashboard/advertisement/groups/','save_group')?>">
    <table class="entry-form" border="0" cellspacing="1" cellpadding="0">
      <tr>
        <td colspan="1" class="header">Add Group (<span class="required">*</span> - required field)</td>
      </tr>
      <tr>
        <td class="subheader">Name <span class="required">*</span></td>
      </tr>
      <tr>
        <td style="width: 33%"><input type="text" name="agName" style="width: 100%" value="" /></td>
      </tr>
      <tr>
        <td colspan="1" class="header"><input type="submit" name="add" value="Add Field" />
      </tr>
    </table>
    <br>
  </form>
<? } ?>
</div>