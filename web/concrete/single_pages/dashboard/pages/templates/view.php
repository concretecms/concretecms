<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Page Types'), false, false);?>

<? if (count($templates) == 0) { ?>
    <br/><strong><?=t('No page types found.')?></strong><br/><br>
<? } else { ?>

<div class="row">

    <? foreach($templates as $pt) { ?>
      <div class="col-md-2">
        <div class="thumbnail" style="text-align: center">
            <div style="text-align: center"><?=$pt->getPageTemplateIconImage()?></div>
            <div class="caption">
            <h4><?=$pt->getPageTemplateName()?></h4>
            <p><a href="<?=$this->action('edit', $pt->getPageTemplateID())?>" class="btn btn-default"><?=t('Edit')?></a></p>
            </div>
        </div>
      </div>

    <? } ?>

</div>

<? } ?>
<br/>
<div class="clearfix"><a href="<?=$this->url('/dashboard/pages/templates/add')?>" class="btn btn-primary"><?=t('Add Template')?></a></div>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>