<? 
defined('C5_EXECUTE') or die("Access Denied.");
?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Statistics'))?>

<h2><?=t("Recent Activity")?></h2>

<div class="row">


<div class="span-pane-half">
<h3><?=t('Visits')?></h3>

</div>

<div class="span-pane-half">
<h3><?=t('Registrations')?></h3>
</div>

</div>

<div class="row">


<div class="span-pane-half">
<h3><?=t('Pages Created')?></h3>

</div>

<div class="span-pane-half">
<h3><?=t('Downloads')?></h3>
</div>

</div>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
