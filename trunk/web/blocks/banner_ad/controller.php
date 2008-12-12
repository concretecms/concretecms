<?
	defined('C5_EXECUTE') or die(_("Access Denied."));
	class BannerAdBlockController extends BlockController {
		
		var $pobj;
		 
		protected $btTable = 'btBannerAd';
		protected $btInterfaceWidth = "400";
		protected $btInterfaceHeight = "200";
		
		public $adCode = '';  
		
		/** 
		 * Used for localization. If we want to localize the name/description we have to include this
		 */
		public function getBlockTypeDescription() {
			return t("So you can add advertising to your site with an ad network like Google Ads.");
		}
		
		public function getBlockTypeName() {
			return t("Banner Ad");
		}
				
		function __construct($obj = null) {		
			parent::__construct($obj);	 
		}
		
		function view(){  
			$this->set('adCode', $this->adCode); 
		}
		
		function save($data) { 
			$args['adCode'] = isset($data['adCode']) ? trim($data['adCode']) : ''; 
			parent::save($args); 
		} 
		
	}
	
?>