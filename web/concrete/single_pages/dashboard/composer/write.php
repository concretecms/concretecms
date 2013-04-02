<? defined('C5_EXECUTE') or die("Access Denied."); ?>


<? if (is_object($composer)) { ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper($composer->getComposerName(), false, false, false)?>
	<form method="post" class="form-horizontal" action="<?=$this->action('save', $composer->getComposerID())?>">
	<div class="ccm-pane-body">
	<? foreach($fieldsets as $cfl) { ?>
		<fieldset style="margin-bottom: 0px">
			<? if ($cfl->getComposerFormLayoutSetName()) { ?>
				<legend><?=$cfl->getComposerFormLayoutSetName()?></legend>
			<? } ?>
			<? $controls = ComposerFormLayoutSetControl::getList($cfl);

			foreach($controls as $con) { 
				$cnp = new Permissions($con);
				if ($cnp->canAccessComposerFormLayoutSetControl()) { ?>
					<? $con->render(); ?>
				<? } ?>
				
			<? } ?>

		</fieldset>

	<? } ?>

	</div>
	<div class="ccm-pane-footer">
		<button type="submit" class="btn btn-primary pull-right"><?=t('Publish')?></button>
	</div>
	</form>
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>


<? } else { ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Composer'), false, 'span10 offset1')?>

	<? if (count($composers) > 0) { ?>
	<h3><?=t('What would you like to write?')?></h3>
	<ul class="item-select-list">
	<? foreach($composers as $cmp) { ?>
		<li class="item-select-page"><a href="<?=$this->url('/dashboard/composer/write', $cmp->getComposerID())?>"><?=$cmp->getComposerName()?></a></li>
	<? } ?>
	</ul>
	<? } else { ?>
		<p><?=t('You have not setup any page types for Composer.')?></p>
	<? } ?>


	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>


<? } ?>