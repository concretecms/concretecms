<?php defined('C5_EXECUTE') or die('Access Denied.');
?>

<form method="post" action="<?php echo $view->action('submit'); ?>">
    <?= $form->getAutocompletionDisabler(); ?>
	<fieldset>
		<legend><?=  t('Basic Details'); ?></legend>
		
		<div class="form-group">
			<label for="uName" class="col-form-label"><?php echo t('Username'); ?></label>
			<div class="input-group align-items-center" >
				<?php echo $form->text('uName', ['autofocus' => 'autofocus', 'autocomplete' => 'off']); ?>
				<small  class="text-muted ml-1" >
			      	* <?=  t('Required'); ?>
				</small>
			</div>
		</div>
		

		<div class="form-group">
			<label for="uPassword" class="col-form-label"><?php echo t('Password'); ?></label>
			<div class="input-group align-items-center">
				<?php echo $form->password('uPassword', ['autocomplete' => 'off']); ?>
				<small  class="text-muted ml-1" >
			      	* <?=  t('Required'); ?>
				</small>
			</div>
		</div>

		<div class="form-group">
			<label for="uEmail" class="col-form-label"><?php echo t('Email Address'); ?></label>
			<div class="input-group align-items-center">
				<?php echo $form->email('uEmail'); ?>
				<small  class="text-muted ml-1" >
			     	* <?=  t('Required'); ?>
				</small>
			</div>
		</div>

		<?php if (count($locales)) { // "> 1" because en_US is always available?>
		<div class="form-group">
			<label for="uEmail" class="col-form-label"><?php echo t('Language'); ?></label>
			<div>
				<?php echo $form->select('uDefaultLanguage', $locales, Localization::activeLocale()); ?>
			</div>
		</div>
		<?php
} ?>
	</fieldset>

<?php if (count($attribs) > 0) {
    ?>
	<fieldset>
		<legend><?=  t('Registration Data'); ?></legend>

	<?php foreach ($attribs as $ak) {
        if (in_array($ak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) {
            ?>
		<div class="form-group">
        	<label class="col-form-label"><?php echo $ak->getAttributeKeyDisplayName(); ?></label>
        	<div>
                <?php $ak->render(new \Concrete\Core\Attribute\Context\DashboardFormContext(), null, false); ?>
            </div>
        </div>
        <?php
        } ?>
    <?php
    } ?>

	</fieldset>
<?php
} ?>

	<fieldset>
		<legend><?= t('Groups'); ?></legend>
		<div class="form-group">
			<label class="col-form-label"><?= t('Place this user into groups'); ?></label>

		<?php foreach ($gArray as $g) {
        $gp = new Permissions($g);
        if ($gp->canAssignGroup()) {
            ?>
			<div class="form-check">
				    <?php  echo $form->checkbox('gID[]', $g->getGroupID(), isset($_POST['gID']) && is_array($_POST['gID']) && in_array($g->getGroupID(), $_POST['gID'])); ?>
					<label class="form-check-label"  > <?=  $g->getGroupDisplayName(); ?> <label> 
			</div>
		<?php
        }
    } ?>

		</div>
    </fieldset>
	<?php echo $token->output('submit'); ?>
	<div class="ccm-dashboard-form-actions-wrapper">
		<div class="ccm-dashboard-form-actions">
			<a href="<?= URL::to('/dashboard/users/search'); ?>" class="btn btn-secondary float-left"><?=  t('Cancel'); ?></a>
			<?php echo $form->submit('add', t('Add'), ['class' => 'btn btn-primary float-right']); ?>
		</div>
	</div>
</form>
