<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$mi = Marketplace::getInstance();
if ($mi->hasConnectionError() && $mi->getConnectionError() == Marketplace::E_MARKETPLACE_SUPPORT_MANUALLY_DISABLED) { ?>
	<p><?=t('Marketplace integration disabled in configuration file.')?></p>

<? } else {

	$h = Loader::helper('concrete/interface');
	?>
	
	<?=t('Your site is <strong>not</strong> connected to the concrete5 community.')?>
	<?
	if ($mi->hasConnectionError()) { ?>
		<div class="ccm-error"><br/>
		<?
		switch($mi->getConnectionError()) {
			default:
				case Marketplace::E_INVALID_BASE_URL:
					print t('The base URL of your site does not match a registered instance of the site. Please click below to authenticate your site again.');
					
					break;
	
		}
		?>
		</div>
		<?
	}
	?>
	
	<br/>
	
	<? print $h->button(t('Connect to Community'), $this->url('/dashboard/settings/marketplace'))?>
<? } ?>