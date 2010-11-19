<?php 
	defined('C5_EXECUTE') or die("Access Denied.");
	class GoogleMapBlockController extends BlockController {
		
		var $pobj;
		
		protected $btTable = 'btGoogleMap';
		protected $btInterfaceWidth = "400";
		protected $btInterfaceHeight = "220";
		protected $btCacheBlockRecord = true;
		protected $btCacheBlockOutput = true;
		protected $btCacheBlockOutputOnPost = true;
		protected $btCacheBlockOutputForRegisteredUsers = true;

		public $title = "";
		public $api_key = "";
		public $location = "";
		public $latitude = "";
		public $longitude = "";
		public $zoom = 14;								
		
		/** 
		 * Used for localization. If we want to localize the name/description we have to include this
		 */
		public function getBlockTypeDescription() {
			return t("Enter an address and a Google Map of that location will be placed in your page.");
		}
		
		public function getBlockTypeName() {
			return t("Google Map");
		}		
		
		public function getJavaScriptStrings() {
			return array(
				'maps-api-key' => t('Please enter a valid Google Maps API key.'),
				'maps-zoom' => t('Please enter a zoom number from 0 to 17.')
			);
		}
		
		function __construct($obj = null) {		
			parent::__construct($obj);	 
			if(!$this->title) $this->title=t("My Map"); 
		}		
		
		public function add() {
			$db = Loader::db();		
			$q = 'SELECT api_key FROM '.$this->btTable.' WHERE api_key!="" ';
			$this->api_key = $db->getOne($q);
			$this->title=t("My Map");
		}
		
		function view(){ 
			$this->set('bID', $this->bID);	
			$this->set('title', $this->title);
			$this->set('api_key', $this->api_key);
			$this->set('location', $this->location);
			$this->set('latitude', $this->latitude);
			$this->set('longitude', $this->longitude);
			$this->set('zoom', $this->zoom);			
		}
		
		function save($data) { 
			$args['title'] = isset($data['title']) ? trim($data['title']) : '';
			$args['api_key'] = isset($data['api_key']) ? trim($data['api_key']) : '';
			$args['location'] = isset($data['location']) ? trim($data['location']) : '';
			$args['zoom'] = (intval($data['zoom'])>=0 && intval($data['zoom'])<=17) ? intval($data['zoom']) : 14;
			
			if( strlen($args['location'])>0 ){
				$geoCodeHelper = new GoogleGeoCodeHelper( $args['api_key'] );
				$geoCodeHelper->loadGeoCodeData($args['location']);
				$coords=$geoCodeHelper->getCoords();
				$args['latitude']=floatval($coords[1]);
				$args['longitude']=floatval($coords[0]);
			}else{
				$args['latitude']=0;
				$args['longitude']=0;
			}
			
			parent::save($args);
		}
		
	}
	
	
	/** 
	 * @access private
	 */
	class GoogleGeoCodeHelper{
	
		private $key;
		private $xml;
		
		public function __construct($key){
			$this->key=$key;
		}
		
		//region string is something like "portland, OR"
		public function loadGeoCodeData($regionString){
			$fh = Loader::helper('file');
			$url = 'http://maps.google.com/maps/geo?q='.urlencode($regionString).'&output=xml&key='.$this->key;
			// Retrieve the URL contents 
			$xml = $fh->getContents($url);
			// Parse the returned XML file
			//echo htmlspecialchars($xml); 
			if(function_exists('mb_detect_encoding')) {
				$enc = mb_detect_encoding($xml);
				$xml = mb_convert_encoding($xml, APP_CHARSET, $enc);
			}
			try {
				$this->xmlObj = new SimpleXMLElement($xml);
			} catch (Exception $e) {
			}
		}
	
		public function getCoords(){
			if(!$this->xmlObj) return 'No XML Loaded';	
			//array of latitude, longitute, and altitude
			$this->coords= explode(",", $this->xmlObj->Response->Placemark->Point->coordinates);
			$this->coords[]=$this->xmlObj->Response->Placemark->AddressDetails[Accuracy];
			//echo $this->xmlObj->Response->Placemark->AddressDetails[Accuracy].'<br>';
			return $this->coords;
		}
		
		public function getKey(){return $key;}
	}
?>