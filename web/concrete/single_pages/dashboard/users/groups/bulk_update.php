<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Bulk Update'));?>

<? if (is_array($selectedGroups)) { ?>

<h3><?=t('5. Confirm')?></h3>
<? if ($gParent instanceof Group) { ?>
<p><?=t('Move the following group(s) beneath <strong>%s</strong>.', $gParent->getGroupDisplayName())?></p>
<? } else { ?> 
<p><?=t('Move the following group(s) <strong>to the top level of groups</strong>.', $gParent->getGroupDisplayName())?></p>
<? } ?>

<ul>
<? foreach($selectedGroups as $g) { ?>
	<li><?=$g->getGroupDisplayName()?></li>
<? } ?>
</ul>

<form method="post" action="<?=$this->action('confirm')?>">
	<input type="hidden" name="gParentNodeID" value="<?=$_REQUEST['gParentNodeID']?>" />
	<? foreach($_REQUEST['gID'] as $gID) { ?>
		<input type="hidden" name="gID[]" value="<?=$gID?>" />
	<? } ?>
	<br/>
	<input type="hidden" name="gName" value="<?=$_REQUEST['gName']?>" />
	<div style="text-align:center">
		<button class="btn btn-primary btn-large"><?=t('Move Groups')?></button>
	</div>
</form>

<? } else if (is_array($groups)) { ?>

<form action="<?=$this->action('move')?>" method="post" data-form="move-groups">
	
<div class="clearfix">
<div class="span4">

<h3><?=t('2. Choose Groups to Move')?></h3><br/>

	<label class="checkbox" style="user-select: none; -moz-user-select: none; -webkit-user-select: none">
		<input data-toggle="checkbox" type="checkbox" /> <b><?=t('Select All')?></b>
	</label>
	<hr/>

	<div id="group-list">

	<? foreach($groups as $g) { ?>
		<label class="checkbox">
			<input name="gID[]" type="checkbox" <? if (is_array($_POST['gID']) && in_array($g->getGroupID(), $_POST['gID'])) { ?>checked<? } ?> value="<?=$g->getGroupID()?>" /> <?=$g->getGroupDisplayName()?>
		</label>
	<? } ?>

	</div>

</div>

<div class="span4">
<?
	$guestGroupNode = GroupTreeNode::getTreeNodeByGroupID(GUEST_GROUP_ID);
	$registeredGroupNode = GroupTreeNode::getTreeNodeByGroupID(REGISTERED_GROUP_ID);
?>
<h3><?=t('3. Choose New Parent Location')?></h3><br/>

    <div class="nested-groups-tree" data-groups-tree="<?=$tree->getTreeID()?>">
    </div>
    <?=$form->hidden('gParentNodeID')?>
    
    <script type="text/javascript">
    $(function() {
       $('[data-groups-tree=<?=$tree->getTreeID()?>]').ccmgroupstree({
          'treeID': '<?=$tree->getTreeID()?>',
          'chooseNodeInForm': true,
          <? if ($this->controller->isPost()) { ?> 
             'selectNodeByKey': '<?=$_POST['gParentNodeID']?>',
          <? } ?>
		removeNodesByID: ['<?=$guestGroupNode->getTreeNodeID()?>','<?=$registeredGroupNode->getTreeNodeID()?>'],
          'onSelect': function(select, node) {
             if (select) {
                $('input[name=gParentNodeID]').val(node.data.key);
             } else {
                $('input[name=gParentNodeID]').val('');
             }
          }
       });
    });
    </script>            

</div>
<div class="span3">
	<h3><?=t('4. Move')?></h3>
	<p><?=t('Move selected groups (left column) beneath selected group (right column)')?></p>
	<button type="submit" class="btn btn-primary"><?=t('Move')?></button>
</div>

</div>

	<input type="hidden" name="gName" value="<?=$_REQUEST['gName']?>" />
</form>

<script type="text/javascript">
$(function() {
	$('input[data-toggle=checkbox]').on('click', function() {
		if ($(this).is(':checked')) {
			$('#group-list input[type=checkbox]').prop('checked', true);
		} else {
			$('#group-list input[type=checkbox]').prop('checked', false);

		}
	});
})
</script>



<? } else { ?>

<form action="<?=$this->action('search')?>" class="form-horizontal">
	<h3><?=t('1. Search for Groups to Move')?></h3>
	<div class="control-groups">
		<?=$form->label('gName', t('Search Groups'))?>
		<div class="controls">
			<?=$form->text('gName')?>
			<button type="submit" name="search" class="btn"><?=t('Search')?></button>
		</div>
	</div>
</form>

<? } ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
