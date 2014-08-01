<?php defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use Concrete\Core\Attribute\Key\Key as AttributeKey;
$form = Loader::helper('form');
$txt = Loader::helper('text');?>
<?php if (in_array($this->controller->getTask(), array('update_set', 'update_set_attributes', 'edit', 'delete_set'))) { ?>

		<div class="row">
		<div class="span-pane-half">
		<h3><?=t('Update Set Details')?></h3>
	
		<?php if ($set->isAttributeSetLocked()) { ?>
			<div class="info block-message alert-message">
				<p><?php echo t('This attribute set is locked. It cannot be deleted, and its handle cannot be changed.')?></p>
			</div>	
		<?php } ?>

		<form class="" method="post" action="<?php echo $view->action('update_set')?>">
			<input type="hidden" name="asID" value="<?php echo $set->getAttributeSetID()?>" />
			<?php echo Loader::helper('validation/token')->output('update_set')?>
			<div class="clearfix">
				<?php echo $form->label('asHandle', t('Handle'))?>
				<div class="input">
					<?php if ($set->isAttributeSetLocked()) { ?>
						<?php echo $form->text('asHandle', $set->getAttributeSetHandle(), array('disabled' => 'disabled'))?>
					<?php } else { ?>
						<?php echo $form->text('asHandle', $set->getAttributeSetHandle())?>
					<?php } ?>
				</div>
			</div>
	
			<div class="clearfix">
				<?php echo $form->label('asName', t('Name'))?>
				<div class="input">
					<?php echo $form->text('asName', $set->getAttributeSetName())?>
				</div>
			</div>

			<div class="clearfix">
				<label></label>
				<div class="input">
					<?php echo $form->submit('submit', t('Update Set'), array('class' => ''))?>
				</div>
			</div>
		</form>

		<?php if (!$set->isAttributeSetLocked()) { ?>	
			<h3><?=t('Delete Set')?></h3>
			<p><?php echo t('Warning, this cannot be undone. No attributes will be deleted but they will no longer be grouped together.')?></p>
			<form method="post" action="<?php echo $view->action('delete_set')?>" class="">
				<input type="hidden" name="asID" value="<?php echo $set->getAttributeSetID()?>" />
				<?php echo Loader::helper('validation/token')->output('delete_set')?>
			
				<div class="clearfix">
					<?php echo $form->submit('submit', t('Delete Set'), array('class' => 'danger'))?>
				</div>
			</form>
		<?php } ?>
		</div>

		<div class="span-pane-half">
		<h3><?=t('Add Attributes to Set')?></h3>
	
		<form class="" method="post" action="<?php echo $view->action('update_set_attributes')?>">
			<input type="hidden" name="asID" value="<?php echo $set->getAttributeSetID()?>" />
			<?php echo Loader::helper('validation/token')->output('update_set_attributes')?>
	
			<?php 
			$cat = AttributeKeyCategory::getByID($set->getAttributeSetKeyCategoryID());
			$list = AttributeKey::getList($cat->getAttributeKeyCategoryHandle());
			$unassigned = $cat->getUnassignedAttributeKeys();
			if (count($list) > 0) { ?>
	
				<div class="clearfix">
					<ul class="inputs-list">
	
						<?php foreach($list as $ak) { 
	
						$disabled = '';
						if (!in_array($ak, $unassigned) && (!$ak->inAttributeSet($set))) { 
							$disabled = array('disabled' => 'disabled');
						}
		
						?>
							<li>
								<label>
									<?php echo $form->checkbox('akID[]', $ak->getAttributeKeyID(), $ak->inAttributeSet($set), $disabled)?>
									<span><?php echo $ak->getAttributeKeyDisplayName()?></span>
									<span class="help-inline"><?php echo $ak->getAttributeKeyHandle()?></span>
								</label>
							</li>	
						<?php } ?>
					</ul>
				</div>
		
				<div class="clearfix">
					<?php echo $form->submit('submit', t('Update Attributes'), array('class' => ''))?>
				</div>
			<?php } else { ?>
				<p><?php echo t('No attributes found.')?></p>
			<?php } ?>
	
		</form>
		</div>
		</div>
		</div>




<?php } else if($this->controller->getTask() == 'category' || $this->controller->getTask() == 'add_set'){ ?>

	<form method="post" action="<?php echo $view->action('add_set')?>">

	<?php if (count($sets) > 0) { ?>
	
		<div class="ccm-attribute-sortable-set-list">
		
			<?php foreach($sets as $asl) { ?>
				<div class="ccm-group" id="asID_<?php echo $asl->getAttributeSetID()?>">
                    <i class="ccm-group-sort fa fa-arrows-v" style="padding: 0 4px 0 4px;"></i>

					<a class="ccm-group-inner" href="<?php echo $view->url('/dashboard/system/attributes/sets/', 'edit', $asl->getAttributeSetID())?>">
                        <i class="fa fa-cubes"></i>
                        <?php echo $asl->getAttributeSetDisplayName()?>
                    </a>
				</div>
			<?php } ?>
		</div>

	<?php } else { ?>
		<?php echo t('No attribute sets currently defined.')?>
	<?php } ?>

	<br/>
	
	<h3><?=t('Add Set')?></h3>

	<input type="hidden" name="categoryID" value="<?php echo $categoryID?>" />
	<?php echo Loader::helper('validation/token')->output('add_set')?>
	<div class="form-group">
		<?php echo $form->label('asHandle', t('Handle'))?>
		<?php echo $form->text('asHandle')?>
	</div>
	
	<div class="form-group">
		<?php echo $form->label('asName', t('Name'))?>
		<?php echo $form->text('asName')?>
	</div>
	
	<div class="form-group">
		<label class="control-label"></label>
		<?php echo $form->submit('submit', t('Add Set'), array('class' => 'btn'))?>
	</div>

	</form>
	

<?php } else { // Attribute Category List  ?>

        <h3><?=t('Attribute Categories')?></h3>
		<span class="help-block"><?php echo t('Attribute Categories are used to group different types of sets.')?></span>
		<div class="">
			<?php 
			if(count($categories) > 0) {
				foreach($categories as $cat) { ?>
					<div class="ccm-group" id="acID_<?php echo $cat->getAttributeKeyCategoryID()?>">

						<a class="ccm-group-inner" href="<?php echo $view->url('/dashboard/system/attributes/sets/', 'category', $cat->getAttributeKeyCategoryID())?>">
                            <i class="fa fa-cubes"></i>
                            <?php echo $txt->unhandle($cat->getAttributeKeyCategoryHandle())?>
                        </a>
					</div>
				<?php } 
			} else {
				echo t('No attribute categories currently defined.');
			} ?>
		</div>

<?php } ?>

