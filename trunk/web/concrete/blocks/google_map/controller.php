<?
	class GoogleMapBlockController extends BlockController {
		
		var $pobj;
		
		protected $btDescription = "Enter an address and a Google Map of that location will be placed in your page.";
		protected $btName = "Google Map";
		protected $btTable = 'btGoogleMap';
		protected $btInterfaceWidth = "400";
		protected $btInterfaceHeight = "220";
		
		public $title = "My Map";
		public $api_key = "";
		public $location = "";
		public $latitude = "";
		public $longitude = "";
		public $zoom = 14;								
		
		
		public function add() {
			$db = Loader::db();		
			$q = 'SELECT api_key FROM '.$this->btTable.' WHERE api_key!="" ';
			$this->api_key = $db->getOne($q);
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
	
	

	class GoogleGeoCodeHelper{
	
		private $key;
		private $xml;
		
		public function __construct($key){
			$this->key=$key;
		}
		
		//region string is something like "portland, OR"
		public function loadGeoCodeData($regionString){
			$url = 'http://maps.google.com/maps/geo?q='.urlencode($regionString).'&output=xml&key='.$this->key;
			// Retrieve the URL contents 
			$xml = file_get_contents($url);
			// Parse the returned XML file
			//echo htmlspecialchars($xml); 
			$enc = mb_detect_encoding($xml);
			$xml = mb_convert_encoding($xml, 'UTF-8', $enc);
			$this->xmlObj = new SimpleXMLElement($xml);
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