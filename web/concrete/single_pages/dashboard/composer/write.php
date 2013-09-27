<? defined('C5_EXECUTE') or die("Access Denied."); ?>


<? if (is_object($pagetype)) { ?>

<div id="ccm-panel-page" class="ccm-panel ccm-panel-left ccm-panel-active ccm-panel-loaded">
	<div class="ccm-panel-content-wrapper">
	<div class="ccm-panel-content ccm-panel-content-visible">
	<?
		Loader::element('panels/page', array(
			'pagetype' => $pagetype,
			'draft' => $draft
		));
	?>
	</div>
	
</div>
</div>


<? } else { ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Composer'), false, 'span10 offset1')?>

	<? if (count($pagetypes) > 0) { ?>
	<h3><?=t('What would you like to write?')?></h3>
	<ul class="item-select-list">
	<? foreach($pagetypes as $pt) { 
		$ccp = new Permissions($pt);
		if ($ccp->canEditPageTypeInComposer()) { 
		?>
		<li class="item-select-page"><a href="<?=$this->url('/dashboard/composer/write', 'composer', $pt->getPageTypeID())?>"><?=$pt->getPageTypeName()?></a></li>
		<? } ?>
	<? } ?>
	</ul>
	<? } else { ?>
		<p><?=t('You do not have any page types.')?></p>
	<? } ?>


	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>


<? } ?>