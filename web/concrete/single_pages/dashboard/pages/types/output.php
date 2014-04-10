<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="container">
<div class="row">

<? foreach($pagetype->getPageTypePageTemplateObjects() as $pt) { ?>
	
	  <div class="col-md-3">
	    <div class="thumbnail" style="text-align: center">
	        <div style="text-align: center"><?=$pt->getPageTemplateIconImage()?></div>
	        <div class="caption">
	        <h4><?=$pt->getPageTemplateName()?></h4>
	        <p><a href="<?=$view->action('edit_defaults', $pagetype->getPageTypeID(), $pt->getPageTemplateID())?>" target="_blank" class="btn btn-default"><?=t('Edit Defaults')?></a></p>
	        </div>
	    </div>
	  </div>

<? } ?>

</div>
</div>