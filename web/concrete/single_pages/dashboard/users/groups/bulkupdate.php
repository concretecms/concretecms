<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');?>

<? if (is_array($selectedGroups)) { ?>

<h2><?=t('Confirm')?></h2>
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

<form method="post" action="<?=$view->action('confirm')?>" role="form">
    <input type="hidden" name="gParentNodeID" value="<?=$_REQUEST['gParentNodeID']?>" />
    
	<? foreach($_REQUEST['gID'] as $gID) { ?>
		<input type="hidden" name="gID[]" value="<?=$gID?>" />
	<? } ?>
	<br/>
	<input type="hidden" name="gName" value="<?=$_REQUEST['gName']?>" />
	
	<div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?php echo $interface->submit(t('Move Groups'), '', 'right', 'btn-primary'); ?>
        </div>
    </div>
</form>

<? } else if (is_array($groups)) { ?>

<form action="<?=$view->action('move')?>" method="post" data-form="move-groups">

    <div class="row">
        <div class="col-md-6">
            <h2><?=t('Choose Groups to Move')?></h2>
    
        	<label class="checkbox" style="user-select: none; -moz-user-select: none; -webkit-user-select: none">
        		<input data-toggle="checkbox" type="checkbox" /> <b><?=t('Select All')?></b>
        	</label>
        	
        	<div id="group-list">
                <? foreach($groups as $g) { ?>
            		<label class="checkbox">
            			<input name="gID[]" type="checkbox" <? if (is_array($_POST['gID']) && in_array($g->getGroupID(), $_POST['gID'])) { ?>checked<? } ?> value="<?=$g->getGroupID()?>" /> <?=$g->getGroupDisplayName()?>
            		</label>
            	<? } ?>
            </div>
        </div>
        
        <div class="col-md-6">
            <h2><?=t('Choose New Parent Location')?></h2>
            
            <?=$form->hidden('gParentNodeID')?>
            
            <div class="nested-groups-tree" data-groups-tree="<?=$tree->getTreeID()?>">
            
            </div>
            
            <?
            $guestGroupNode = GroupTreeNode::getTreeNodeByGroupID(GUEST_GROUP_ID);
            $registeredGroupNode = GroupTreeNode::getTreeNodeByGroupID(REGISTERED_GROUP_ID);
            ?>
        </div>
    </div>
    
    <hr/>
    
    <div class="row">
        
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <h3><?=t('Move')?></h3>
            <p><?=t('Move selected groups (left column) beneath selected group (right column)')?></p>
            
            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <?php echo $interface->submit(t('Move'), '', 'right', 'btn-primary'); ?>
                </div>
            </div>
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

<script type="text/javascript">
    $(function() {
       $('[data-groups-tree=<?=$tree->getTreeID()?>]').concreteGroupsTree({
          'treeID': '<?=$tree->getTreeID()?>',
          'chooseNodeInForm': 'single',
          <? if ($this->controller->isPost()) { ?> 
             'selectNodeByKey': '<?=$_POST['gParentNodeID']?>',
          <? } ?>
          'removeNodesByID': ['<?=$guestGroupNode->getTreeNodeID()?>','<?=$registeredGroupNode->getTreeNodeID()?>'],
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

<? } else { ?>

<form method="POST" action="<?=$view->action('search')?>">
	<h2><?=t('Search for Groups to Move')?></h2>
	
	<div class="row">
	    <div class="col-md-6">
	        <fieldset>
            	<div class="form-group">
            		<?=$form->label('gName', t('Search Groups'))?>
                    <?=$form->text('gName')?>
            	</div>
            </fieldset>
            
        	<div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <?php echo $interface->submit(t('Search'), '', 'right', 'btn-primary'); ?>
                </div>
            </div>
	    </div>
	</div>
</form>

<? } ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
