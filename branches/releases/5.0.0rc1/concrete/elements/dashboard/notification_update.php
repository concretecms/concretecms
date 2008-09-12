<?php  if (version_compare($latest_version, APP_VERSION, '>')) { ?>
<div id="ccm-dashboard-notification">
The latest version of Concrete5 is <strong><?php echo $latest_version?></strong>. You are running <?php echo APP_VERSION?>. <a href="<?php echo APP_VERSION_LATEST_DOWNLOAD?>">Upgrade Now</a>!
</div>
<?php  } ?>
