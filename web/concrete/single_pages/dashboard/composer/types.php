<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Composers'))?>
<div class="clearfix">

<a href="<?=$this->url('/dashboard/composer/types/add')?>" class="btn pull-right"><?=t('Add Composer')?></a>

</div>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>