<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php $ih = Loader::helper('concrete/ui'); ?>

    <form method="post" class="form-horizontal" action="<?=$view->action('submit')?>">
		<fieldset>
    		<legend><?=t('Basic Details')?></legend>
			
			<div class="row">
				<div class="form-group">
					<label for="uName" class="control-label col-sm-3"><?=t('Username')?></label>
					<div class="col-sm-7">
						<div class="input-group">
						<?=$form->text('uName', array('autocomplete' => 'off'))?>
						<span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="form-group">
					<label for="uPassword" class="control-label col-sm-3"><?=t('Password')?></label>
					<div class="col-sm-7">
						<div class="input-group">
						<?=$form->password('uPassword', array('autocomplete' => 'off'))?>
						<span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="form-group">
					<label for="uEmail" class="control-label col-sm-3"><?=t('Email Address')?></label>
					<div class="col-sm-7">
						<div class="input-group">
						<?=$form->email('uEmail')?>
						<span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
						</div>
					</div>
				</div>
			</div>

			<?php if (count($locales)) { // "> 1" because en_US is always available ?>
		
			<div class="row">
				<div class="form-group">
					<label for="uEmail" class="control-label col-sm-3"><?=t('Language')?></label>
					<div class="col-sm-7">
					<?php echo $form->select('uDefaultLanguage', $locales, Localization::activeLocale());
    ?>
					</div>
				</div>
			</div>


			<?php 
} ?>
    	</fieldset>

	   	<?php if (count($attribs) > 0) {
    ?>

	   		<fieldset>
	   			<legend><?=t('Registration Data')?></legend>

				<?php foreach ($attribs as $ak) {
    if (in_array($ak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) {
        ?>
					<div class="row">
	                	<div class="form-group">
	                    	<label class="control-label col-sm-3"><?=$ak->getAttributeKeyDisplayName()?></label>
	                    	<div class="col-sm-7">
		                        <?php $ak->render('form', null, false)?>
		                    </div>
		                </div>
		            </div>
	                <?php 
    }
    ?>
	            <?php 
}
    ?>


	   		</fieldset>

		<?php 
} ?>


		<fieldset>
			<legend><?=t('Groups')?></legend>
			<?php foreach ($gArray as $g) {
    $gp = new Permissions($g);
    if ($gp->canAssignGroup()) {
        ?>
				<div class="row">
				<div class="col-sm-3"></div>
				<div class="col-sm-7">
					<div class="checkbox">
					<label>
						<input type="checkbox" name="gID[]" value="<?=$g->getGroupID()?>" <?php if (isset($_POST['gID']) && is_array($_POST['gID']) && in_array($g->getGroupID(), $_POST['gID'])) {
    ?> checked <?php 
}
        ?>>
						<?=$g->getGroupDisplayName()?>
					</label>
					</div>
				</div>
				</div>
	        <?php 
    }
} ?>
        </fieldset>
	<?=$token->output('submit');?>

	<div class="ccm-dashboard-form-actions-wrapper">
	<div class="ccm-dashboard-form-actions">
		<a href="<?=View::url('/dashboard/users/search')?>" class="btn btn-default pull-left"><?=t('Cancel')?></a>
		<?=Loader::helper("form")->submit('add', t('Add'), array('class' => 'btn btn-primary pull-right'))?>
	</div>
	</div>
    </form>
