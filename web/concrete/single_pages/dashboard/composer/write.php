<? defined('C5_EXECUTE') or die("Access Denied."); ?>


<? if (is_object($composer)) { ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper($composer->getComposerName(), false, false, false)?>
	<form method="post" data-form="composer" class="form-horizontal">
	<div class="ccm-pane-body">
		<? Loader::helper('composer/form')->display($composer, $draft); ?>
	</div>
	<div class="ccm-pane-footer">
		<? Loader::helper('composer/form')->displayButtons(); ?>
	</div>


	</form>

	<style type="text/css">
		button[data-composer-btn=save] {
			margin-left: 10px;
		}
		button[data-composer-btn=publish] {
			margin-left: 10px;
		}

	</style>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>


<? } else { ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Composer'), false, 'span10 offset1')?>

	<? if (count($composers) > 0) { ?>
	<h3><?=t('What would you like to write?')?></h3>
	<ul class="item-select-list">
	<? foreach($composers as $cmp) { 
		$ccp = new Permissions($cmp);
		if ($ccp->canAccessComposer()) { 
		?>
		<li class="item-select-page"><a href="<?=$this->url('/dashboard/composer/write', 'composer', $cmp->getComposerID())?>"><?=$cmp->getComposerName()?></a></li>
		<? } ?>
	<? } ?>
	</ul>
	<? } else { ?>
		<p><?=t('You have not setup any page types for Composer.')?></p>
	<? } ?>


	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>


<? } ?>