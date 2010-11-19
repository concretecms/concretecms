<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php echo t('You are logged in as <b>%s</b>. You logged in on <b>%s</b>.', $uName, $uLastLogin)?> 
<ul class="ccm-dashboard-list">
<?php  if (isset($uLastActivity)) { ?>
	<li><?php echo t('Number of visits since your previous login')?>: <b><?php echo $uLastActivity?></b></li>
<?php  } ?>
<li><?php echo t('Total visits')?>: <b><?php echo $totalViews?></b></li>
<li><?php echo t('Total page versions')?>: <b><?php echo $totalVersions?></b></li>
<li><?php echo t('Last edit')?>: <b><?php echo $lastEditSite?></b></li>
<li><?php echo t('Last login')?>: <b><?php echo $lastLoginSite?></b></li>
<li><?php echo t('Total pages in edit mode')?>: <b><?php echo $totalEditMode?></b></li>
<li><?php echo t('Total form submissions')?>: <a href="<?php echo $this->url('/dashboard/reports/forms/')?>"><b><?php echo $totalFormSubmissionsToday?></b> <?php echo t('today')?></a> (<b><?php echo $totalFormSubmissions?></b> <?php echo t('total')?>)</li>

</ul>