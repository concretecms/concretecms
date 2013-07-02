<?
	defined('C5_EXECUTE') or die("Access Denied.");
/**
 * Controller for the Google Maps block, which can display a map on a page.
 *
 * @package Blocks
 * @subpackage Google Map
 * @author Ryan Tyler <ryan@concrete5.org>
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class Concrete5_Controller_Block_GoogleMap extends BlockController {
		
		protected $btTable = 'btGoogleMap';
		protected $btInterfaceWidth = "400";
		protected $btInterfaceHeight = "200";
		protected $btCacheBlockRecord = true;
		protected $btCacheBlockOutput = true;
		protected $btCacheBlockOutputOnPost = true;
		protected $btCacheBlockOutputForRegisteredUsers = false;

		public $title = "";
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
		
		
		public function validate($args) {
			$error = Loader::helper('validation/error');
			
			if(!is_numeric($args['zoom'])) {
				$error->add(t('Please enter a zoom number from 0 to 21.'));
			}
			
			if($error->has()) {
				return $error;
			}
		}
		
		
		public function on_page_view() {
			$html = Loader::helper('html');
			$c = Page::getCurrentPage();
			if (!$c->isEditMode()) {
				$this->addFooterItem('<script type="text/javascript" src="//maps.google.com/maps/api/js?sensor=true"></script>');
				$this->addFooterItem('<script type="text/javascript"> 
				function googleMapInit' . $this->bID . '() { 
				   try{
					  var latlng = new google.maps.LatLng(' . $this->latitude . ', ' . $this->longitude . ');
					   var mapOptions = {
						 zoom: ' . $this->zoom . ',
						 center: latlng,
						 mapTypeId: google.maps.MapTypeId.ROADMAP,
						 streetViewControl: false,
						 mapTypeControl: false
					  };
					   var map = new google.maps.Map(document.getElementById(\'googleMapCanvas' . $this->bID . '\'), mapOptions);
					   var marker = new google.maps.Marker({
						   position: latlng, 
						   map: map
					   });
				   }catch(e){
				   $("#googleMapCanvas'. $this->bID .'").replaceWith("<p>Unable to display map: "+e.message+"</p>")}
				}
				$(function() {
					var t;
					var startWhenVisible = function (){
						if ($("#googleMapCanvas'. $this->bID .'").is(":visible")){
							window.clearInterval(t);
							googleMapInit' . $this->bID . '();
							return true;
						} 
						return false;
					};
					if (!startWhenVisible()){
						t = window.setInterval(function(){startWhenVisible();},100);      
					}
				});            
				</script>');				
			}
		}
		
		
		public function view(){ 
			$this->set('bID', $this->bID);	
			$this->set('title', $this->title);
			$this->set('location', $this->location);
			$this->set('latitude', $this->latitude);
			$this->set('longitude', $this->longitude);
			$this->set('zoom', $this->zoom);			
		}
		
		public function save($data) { 
			$args['title'] = isset($data['title']) ? trim($data['title']) : '';
			$args['location'] = isset($data['location']) ? trim($data['location']) : '';
			$args['zoom'] = (intval($data['zoom'])>=0 && intval($data['zoom'])<=21) ? intval($data['zoom']) : 14;
			
			if( strlen($args['location'])>0 ){
				$coords = $this->lookupLatLong($args['location']);
				$args['latitude']=floatval($coords['lat']);
				$args['longitude']=floatval($coords['lng']);
			} else {
				$args['latitude']=0;
				$args['longitude']=0;
			}
			
			parent::save($args);
		}
		
		public function lookupLatLong($address) {
			$json = Loader::helper('json');
			$fh = Loader::helper('file');
			
			$base_url = "http://maps.google.com/maps/api/geocode/json?sensor=false";
			$request_url = $base_url . "&address=".urlencode($address);
			
			$res = $fh->getContents($request_url);
			$res = $json->decode($res);
			if(!is_object($res)) { 
				return false;
			}
			switch($res->status) {
				case 'OK':
					$lat = $res->results[0]->geometry->location->lat;
					$lng = $res->results[0]->geometry->location->lng;
					return array('lat'=>$lat,'lng'=>$lng);
					break;
				case 'ZERO_RESULTS':
				case 'OVER_QUERY_LIMIT':
				case 'REQUEST_DENIED':
				case 'INVALID_REQUEST':
					return false;
					break;
			}
		}
		
	}