<?

defined('C5_EXECUTE') or die(_("Access Denied."));

class AdvertisementPackage extends Package {

	protected $pkgDescription = "Add advertisements to your website, track impressions and click-throughs.";
	protected $pkgName = "Advertisement";
	protected $pkgHandle = 'advertisement';
	
	public function install() {
		$pkg = parent::install();
		$db = Loader::db();		
	}




}

?>