<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>

<script type="text/javascript">

CCM_LAUNCHER_SITEMAP = 'explore'; // we need this for when we are moving and copying

$(function() {
	ccmSitemapLoad('<?php echo $instanceID?>', 'explore');
});
</script>

<h1><span><?php echo t('Sitemap')?></span></h1>

<div class="ccm-dashboard-inner" >

	<?php  if ($dh->canRead()) { ?>	
		<div id="ccm-sitemap-message"></div>
	
		<div id="tree" class="ccm-sitemap-explore">
			<ul id="tree-root0" tree-root-node-id="0" sitemap-display-mode="explore" sitemap-instance-id="<?php echo $instanceID?>">
			<?php echo $listHTML?>
			</ul>
		</div>
	<?php  } else { ?>
		<p><?php echo t('You do not have access to the dashboard sitemap.')?></p>
	<?php  } ?>
	
</div>