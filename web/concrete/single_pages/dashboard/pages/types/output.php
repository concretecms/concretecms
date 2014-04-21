<? defined('C5_EXECUTE') or die("Access Denied."); ?>


    <table class="table table-striped">

<? foreach($pagetype->getPageTypePageTemplateObjects() as $pt) { ?>
	

    <tr>
        <td><a href="<?=$view->action('edit', $pt->getPageTemplateID())?>"><?=$pt->getPageTemplateIconImage()?></a></td>
        <td style="width: 100%; vertical-align: middle"><p class="lead" style="margin-bottom: 0px"><?=$pt->getPageTemplateName()?></p></td>
        <td style="vertical-align: middle"><a href="<?=$view->action('edit_defaults', $pagetype->getPageTypeID(), $pt->getPageTemplateID())?>" target="_blank" class="btn btn-default"><?=t('Edit Defaults')?></a></td>
    </tr>

<? } ?>

</table>