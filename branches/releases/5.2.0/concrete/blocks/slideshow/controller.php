<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
class SlideshowBlockController extends BlockController {
	
	var $pobj;
	
	protected $btTable = 'btSlideshow';
	protected $btInterfaceWidth = "550";
	protected $btInterfaceHeight = "400";
	
	public $defaultDuration = 5;	
	public $defaultFadeDuration = 2;	
	
	public $playback = "ORDER";	

	/** 
	 * Used for localization. If we want to localize the name/description we have to include this
	 */
	public function getBlockTypeDescription() {
		return t("Display a running loop of images.");
	}
	
	public function getBlockTypeName() {
		return t("Slideshow");
	}
	
	public function getJavaScriptStrings() {
		return array(
			'choose-file' => t('Choose Image/File'),
			'choose-min-2' => t('Please choose at least two images.')
		);
	}
	
	function __construct($obj = null) {		
		parent::__construct($obj);
		$this->db = Loader::db();
		$this->loadImages();
		$this->set('defaultFadeDuration', $this->defaultFadeDuration);
		$this->set('defaultDuration', $this->defaultDuration);
		$this->set('images', $this->images);
		$this->set('playback', $this->playback);
		$this->set('bID', $this->bID);			
	}	
			
	function view(){
		
	}
	
	function loadImages(){
		if(intval($this->bID)==0) $this->images=array();
		$sortChoices=array('ORDER'=>'position','RANDOM-SET'=>'groupSet asc, position asc','RANDOM'=>'rand()');
		if( !array_key_exists($this->playback,$sortChoices) ) 
			$this->playback='ORDER';
		if(intval($this->bID)==0) return array();
		$sql = "SELECT * FROM btSlideshowImg WHERE bID=".intval($this->bID).' ORDER BY '.$sortChoices[$this->playback];
		$this->images=$this->db->getAll($sql); 
		
		//randomize groups if RANDOM-SET mode
		if($this->playback!='RANDOM-SET') return;		
		$imageGroups=array();
		$imageGroupIds=array();
		$sortedImgs=array();
		foreach($this->images as $imgInfo){
			$imageGroups[$imgInfo['groupSet']][]=$imgInfo;
			if( !in_array($imgInfo['groupSet'],$imageGroupIds) )
				$imageGroupIds[]=$imgInfo['groupSet'];
		}
		shuffle($imageGroupIds);
		foreach($imageGroupIds as $imageGroupId){
			foreach($imageGroups[$imageGroupId] as $imgInfo)
				$sortedImgs[]=$imgInfo;
		}
		$this->images=$sortedImgs;
	}
	
	function delete(){
		$this->db->query("DELETE FROM btSlideshowImg WHERE bID=".intval($this->bID));		
		parent::delete();
	}
	
	function save($data) { 
		$args['playback'] = isset($data['playback']) ? trim($data['playback']) : 'ORDER';
		
		if( count($data['imgBIDs']) ){
			//delete existing images
			$this->db->query("DELETE FROM btSlideshowImg WHERE bID=".intval($this->bID));
			
			//loop through and add the images
			$pos=0;
			foreach($data['imgBIDs'] as $imgBID){ 
				if(intval($imgBID)==0 || $data['fileNames'][$pos]=='tempFilename') continue;
				$vals = array(intval($this->bID),intval($imgBID),$data['fileNames'][$pos],$data['origfileNames'][$pos],$data['thumbPaths'][$pos],
						trim($data['url'][$pos]),intval($data['duration'][$pos]),intval($data['fadeDuration'][$pos]),
						intval($data['groupSet'][$pos]),intval($data['imgHeight'][$pos]),$pos);
				$this->db->query("INSERT INTO btSlideshowImg (bID,image_bID,fileName,origfileName,thumbPath,url,duration,fadeDuration,groupSet,imgHeight,position) values (?,?,?,?,?,?,?,?,?,?,?)",$vals);
				$pos++;
			}
		}
		
		parent::save($args);
	}
	
}

?>