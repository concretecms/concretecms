<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-block-desktop-featured-addon">
	<div class="ccm-block-desktop-featured-addon-inner" style="padding: 1rem">

        <h6><?=t('Featured Add-On')?></h6/>

        <div class="mt-5 mb-1 text-start"><b><?=t('Connect to the new marketplace!')?></b></div>
        <?php
            View::element('dashboard/marketplace_upgrade');
        ?>


	</div>
</div>
