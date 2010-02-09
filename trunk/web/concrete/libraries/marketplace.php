<?

defined('C5_EXECUTE') or die(_("Access Denied."));

class Marketplace {
	
	public function isConnected() {
		$token = Config::get('MARKETPLACE_SITE_TOKEN');
		return $token != '';
	}
	
	public function generateSiteToken() {
		$fh = Loader::helper('file');
		$token = $fh->getContents(MARKETPLACE_URL_CONNECT_TOKEN_NEW);
		return $token;	
	}


}

?>