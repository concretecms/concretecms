<?

defined('C5_EXECUTE') or die(_("Access Denied."));

class AdvertisementPackage extends Package {

	protected $pkgDescription = "Add advertisements to your website, track impressions and click-throughs.";
	protected $pkgName = "Advertisement";
	protected $pkgHandle = 'advertisement';
	
	public function install() {
		$pkg = parent::install();
		$db = Loader::db();
		
		Loader::model('single_page');
		
		BlockType::installBlockTypeFromPackage('advertisement', $pkg);
		$d1 = SinglePage::add('/dashboard/advertisement', $pkg);
		$d2 = SinglePage::add('/dashboard/advertisement/groups', $pkg);
		$d3 = SinglePage::add('/dashboard/advertisement/details', $pkg);
		$d1->update(array('cName' => 'Advertisements', 'cDescription' => 'Add banner ads to your site.'));		
}




}

?>