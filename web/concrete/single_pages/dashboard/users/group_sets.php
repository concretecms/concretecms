<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<?php if (in_array($this->controller->getTask(), array('update_set', 'update_set_groups', 'edit', 'delete_set'))) { 

	echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Set'), false);?>
		
		<div class="row">
		<div class="span-pane-half">
	
		<form class="form-vertical" method="post" action="<?php echo $this->action('update_set')?>">
			
			<input type="hidden" name="gsID" value="<?php echo $set->getGroupSetID()?>" />

			<?php echo Loader::helper('validation/token')->output('update_set')?>

		<fieldset>
			<legend><?=t('Details')?></legend>

			<div class="control-group">
				<?php echo $form->label('gsName', t('Name'))?>
				<div class="controls">
					<?php echo $form->text('gsName', $set->getGroupSetName())?>
				</div>
			</div>

			<div class="control-group">
				<label></label>
				<div class="controls">
					<?php echo $form->submit('submit', t('Update Set'), array('class' => ''))?>
				</div>
			</div>
		</fieldset>
		</form>

		<form method="post" action="<?php echo $this->action('delete_set')?>" class="form-vertical">
		<fieldset>
			<legend><?=t('Delete Set')?></legend>
			<div class="control-group">
			<div class="controls">
				<p><?php echo t('Warning, this cannot be undone. No attributes will be deleted but they will no longer be grouped together.')?></p>
			</div>
			</div>
			
			<input type="hidden" name="gsID" value="<?php echo $set->getGroupSetID()?>" />
			<?php echo Loader::helper('validation/token')->output('delete_set')?>		
			<div class="clearfix">
				<?php echo $form->submit('submit', t('Delete Group Set'), array('class' => 'danger'))?>
			</div>
		</fieldset>
		</form>

		</div>

		<div class="span-pane-half">
	
		<form class="form-vertical" method="post" action="<?php echo $this->action('update_set_groups')?>">
			<input type="hidden" name="gsID" value="<?php echo $set->getGroupSetID()?>" />
			<?php echo Loader::helper('validation/token')->output('update_set_groups')?>

		<fieldset>
			<legend><?=t('Groups')?></legend>
			
	
			<?php 
			$list = $set->getGroups();
			if (count($groups) > 0) { ?>
	
				<div class="control-group">
					<div class="controls">
	
						<?php foreach($groups as $g) { 	

						?>
								<label class="checkbox">
									<?php echo $form->checkbox('gID[]', $g->getGroupID(), $set->contains($g)) ?>
									<span><?php echo $g->getGroupName()?></span>
								</label>
						<?php } ?>
					</div>
				</div>
		
				<div class="control-group">
					<div class="controls">
					<?php echo $form->submit('submit', t('Update Groups'), array('class' => ''))?>
					</div>
				</div>
			<?php } else { ?>
				<div class="control-group">
					<div class="controls">
						<p><?php echo t('No groups found.')?></p>
					</div>
				</div>
			<?php } ?>
		</fieldset>
		</form>
		</div>
		</div>


	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>

<? } else { ?>
 
	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Group Sets'), false, 'span10 offset1');?>
	<? if (PERMISSIONS_MODEL == 'advanced') { ?>
	<form method="post" class="form-horizontal" action="<?php echo $this->action('add_set')?>">


	<?php if (count($groupSets) > 0) { ?>
	
		<div class="ccm-attribute-sortable-set-list">
		
			<?php foreach($groupSets as $gs) { ?>
				<div class="ccm-group" id="asID_<?php echo $gs->getGroupSetID()?>">
					<a class="ccm-group-inner" href="<?php echo $this->url('/dashboard/users/group_sets', 'edit', $gs->getGroupSetID())?>" style="background-image: url(<?php echo ASSETS_URL_IMAGES?>/icons/group.png)"><?php echo $gs->getGroupSetName()?></a>
				</div>
			<?php } ?>
		</div>
	
	<?php } else { ?>
		<p><?php echo t('You have not added any group sets.')?></p>
	<?php } ?>

	<br/>
	
	<h3><?=t('Add Set')?></h3>

	<input type="hidden" name="categoryID" value="<?php echo $categoryID?>" />
	<?php echo Loader::helper('validation/token')->output('add_set')?>
	<div class="control-group">
		<?php echo $form->label('gsName', t('Name'))?>
		<div class="controls">
			<?php echo $form->text('gsName')?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"><?=t('Groups')?></label>
		<div class="controls">
		<? foreach($groups as $g) { ?>
			<label class="checkbox"><?=$form->checkbox('gID[]', $g->getGroupID())?> <span><?=$g->getGroupName()?></span></label>
			
		<? } ?>
		</div>
	</div>
	
	<div class="control-group">
		<label></label>
		<div class="controls">
			<?php echo $form->submit('submit', t('Add Set'), array('class' => 'btn'))?>
		</div>
	</div>

	</form>
	<? } else { ?>
		<p><?=t('You must enable <a href="%s">advanced permissions</a> to use group sets.', $this->url('/dashboard/system/permissions/advanced'))?></p>
	<? } ?>
	
	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>

<? } ?>
