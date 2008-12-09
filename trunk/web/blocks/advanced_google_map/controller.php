<?
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::block('library_file');

class AdvancedGoogleMapBlockController extends BlockController {
	
	var $pobj;
	
	protected $btTable = 'btAdvancedGoogleMap';
	protected $btInterfaceWidth = "400";
	protected $btInterfaceHeight = "220";
	
	public $title = "";
	public $api_key = ""; 	
	public $kml_fID = 0;
	public $w = "100%"; 
	public $h = "400px"; 
	public $map_type='Normal';
	
	public $map_types=array('Normal'=>'G_NORMAL_MAP','Hybrid'=>'G_HYBRID_MAP','Satellite'=>'G_SATELLITE_MAP','Physical'=>'G_PHYSICAL_MAP');
	
	/** 
	 * Used for localization. If we want to localize the name/description we have to include this
	 */
	public function getBlockTypeDescription() {
		return t("The Advanced Google Map lets you add a KML data file.");
	}
	
	public function getBlockTypeName() {
		return t("Advanced Google Map");
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
	}
	
	function view(){ 
		$this->set('bID', $this->bID);	
		$this->set('title', $this->title);
		$this->set('api_key', $this->api_key);	
		$this->set('kml_fID', $this->kml_fID);
		$this->set('w', $this->w);	
		$this->set('kml_file_path', $this->getFileURL() );
		$this->set('map_type_constant', $this->getMapTypeConstant() );
	}
	
	function getFileID(){
		return intval($this->kml_fID);
	}
	
	function getFileObject() {
		return LibraryFileBlockController::getFile($this->fID);
	}		
	
	function getFileURL(){
		$db = Loader::db();
		if(!intval($this->kml_fID)) return '';
		$q = "select filename from btFile where bID=?";
		$r = $db->query( $q, array(intval($this->kml_fID)) );
		$row = $r->fetchRow();
		return BASE_URL.REL_DIR_FILES_UPLOADED . '/' . $row['filename'];
	}
	
	function save($data) { 
		$args['title'] = isset($data['title']) ? trim($data['title']) : '';
		$args['api_key'] = isset($data['api_key']) ? trim($data['api_key']) : '';
		$args['kml_fID'] = (intval($data['fID']))?intval($data['fID']):intval($this->kml_fID); 
		$args['w'] = ($data['w']) ? trim($data['w']) : '100%';
		$args['h'] = ($data['h']) ? trim($data['h']) : '400px';	
		$args['map_type'] = ($data['map_type']) ? trim($data['map_type']) : 'Normal';			
		parent::save($args);
	}
	
	function getMapTypeConstant(){
		$key=$this->map_type;
		if(!array_key_exists($key,$this->map_types))
			return $this->map_types['Normal']; 
		return $this->map_types[$key];		
	}	
}
	
?>