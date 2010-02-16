<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$h = Loader::helper('concrete/interface');
?>

<?=t('Your site is <strong>not</strong> connected to the concrete5 community.')?>
<?
$mi = Marketplace::getInstance();
if ($mi->hasConnectionError()) { 
	switch($mi->getConnectionError()) {
		default:
			print $mi->getConnectionError();
			break;
	}
}
?>
<br/><br/>

<? print $h->button(t('Connect to Community'), $this->url('/dashboard/settings/marketplace'))?>