<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-block-desktop-featured-addon">
	<div class="ccm-block-desktop-featured-addon-inner" style="padding: 1rem">

        <h6><?=t('Featured Add-On')?></h6/>


        <div style="margin-bottom: 1rem; text-align: left"><b><?=t('No Extensions Available')?></b></div>
        <?php
            View::element('dashboard/marketplace_upgrade');
        ?>


	</div>
</div>
