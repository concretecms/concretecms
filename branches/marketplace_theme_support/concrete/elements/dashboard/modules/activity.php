<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?=t('You are logged in as <b>%s</b>. You logged in on <b>%s</b>.', $uName, $uLastLogin)?> 
<ul class="ccm-dashboard-list">
<? if (isset($uLastActivity)) { ?>
	<li><?=t('Number of visits since your previous login')?>: <b><?=$uLastActivity?></b></li>
<? } ?>
<li><?=t('Total visits')?>: <b><?=$totalViews?></b></li>
<li><?=t('Total page versions')?>: <b><?=$totalVersions?></b></li>
<li><?=t('Last edit')?>: <b><?=$lastEditSite?></b></li>
<li><?=t('Last login')?>: <b><?=$lastLoginSite?></b></li>
<li><?=t('Total pages in edit mode')?>: <b><?=$totalEditMode?></b></li>
<li><?=t('Total form submissions')?>: <a href="<?=$this->url('/dashboard/form_results/')?>"><b><?=$totalFormSubmissionsToday?></b> <?=t('today')?></a> (<b><?=$totalFormSubmissions?></b> <?=t('total')?>)</li>

</ul>