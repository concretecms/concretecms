<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('%s Output', $composer->getComposerName()))?>

<div class="row">

<? foreach($composer->getComposerPageTemplateObjects() as $pt) { ?>
	
	  <div class="col-md-2">
	    <div class="thumbnail" style="text-align: center">
	        <div style="text-align: center"><?=$pt->getPageTemplateIconImage()?></div>
	        <div class="caption">
	        <h4><?=$pt->getPageTemplateName()?></h4>
	        <p><a href="<?=$this->action('edit_defaults', $composer->getComposerID(), $pt->getPageTemplateID())?>" target="_blank" class="btn btn-default"><?=t('Edit Defaults')?></a></p>
	        </div>
	    </div>
	  </div>

<? } ?>

</div>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>