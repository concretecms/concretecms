<?php
defined('C5_EXECUTE') or die("Access Denied.");
$mi = Marketplace::getInstance();
if ($mi->hasConnectionError() && $mi->getConnectionError() == Marketplace::E_MARKETPLACE_SUPPORT_MANUALLY_DISABLED) {
    ?>
	<p><?=t('Marketplace integration disabled in configuration file.')?></p>

<?php 
} else {
    $h = Loader::helper('concrete/ui');
    ?>

	<?php
    if ($mi->hasConnectionError()) {
        ?>

        <br>
        <h4><?=t('Marketplace Connection Error')?></h4>
		<div class="alert alert-danger"><p>
		<?php
        switch ($mi->getConnectionError()) {
            case Marketplace::E_INVALID_BASE_URL:
                print t('The base URL of your site does not match a registered instance of the site. Please click below to authenticate your site again.');
                break;
            case Marketplace::E_UNRECOGNIZED_SITE_TOKEN:
                print t('Unable to connect to your project page. Your database contains a marketplace token which Concrete cannot verify.');
                break;
            case Marketplace::E_DELETED_SITE_TOKEN:
                print t('This site is connected to a project page that has been removed. Please reconnect in order to continue using the marketplace.');
                break;
            default:
                print t('Error establishing connection to the concrete5 community. Please check that curl and other required libraries are enabled.');
                break;
    }
        ?>
		</p>

		</div>

		<?php

    }
    ?>
	
	<?php echo $h->button(t('Troubleshoot Connection'), View::url('/dashboard/extend/connect'), '', 'btn-primary')?>
<?php 
} ?>