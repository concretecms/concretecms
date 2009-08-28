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
			'choose-min-2' => t('Please choose at least two images.'),
			'choose-fileset' => t('Please choose a file set.')
		);
	}
	
	function __construct($obj = null) {		
		parent::__construct($obj);
		$this->db = Loader::db();
		if ($this->fsID == 0) {
			$this->loadImages();
		} else {
			$this->loadFileSet();
		}
		$this->randomizeImages();	
		$this->set('defaultFadeDuration', $this->defaultFadeDuration);
		$this->set('defaultDuration', $this->defaultDuration);
		$this->set('fadeDuration', $this->fadeDuration);
		$this->set('duration', $this->duration);
		$this->set('minHeight', $this->minHeight);
		$this->set('fsID', $this->fsID);
		$this->set('fsName', $this->getFileSetName());
		$this->set('images', $this->images);
		$this->set('playback', $this->playback);
		$type = ($this->fsID > 0) ? 'FILESET' : 'CUSTOM';
		$this->set('type', $type);
		$this->set('bID', $this->bID);			
	}	
	
	function view(){
	}
	
	public function on_page_view() {
		$html = Loader::helper('html');
		$this->addHeaderItem($html->javascript('jquery.js'));
	}
	
	function getFileSetName(){
		$sql = "SELECT fsName FROM FileSets WHERE fsID=".intval($this->fsID);
		return $this->db->getOne($sql); 
	}

	function loadFileSet(){
		if (intval($this->fsID) < 1) {
			return false;
		}
        $f = Loader::helper('concrete/file');

		$fakID = $this->db->getOne("SELECT fakID FROM FileAttributeKeys WHERE akName = 'height'"); 

		$sql = "SELECT fsf.fID, fv.fvFilename, fv.fvPrefix, fav.value FROM FileSetFiles fsf, FileVersions fv, FileAttributeValues fav " .
		       "WHERE fsf.fsID = " . $this->fsID . " AND fsf.fID = fv.fID AND fvIsApproved = 1 AND fav.fID = fv.fID " .
		       " AND fav.fvID = fv.fvID AND fav.fakID = " . $fakID;
		$files = $this->db->getAll($sql); 

		$image = array();
		$image['duration'] = $this->duration;
		$image['fadeDuration'] = $this->fadeDuration;
		$image['groupSet'] = 0;
		$image['url'] = '';
		$images = array();
		$maxHeight = 0;
		foreach ($files as $file) {
			$image['fID'] = $file['fID']; 
			$image['fileName'] = $file['fvFilename'];
			$image['fullFilePath'] = $f->getFileRelativePath($file['fvPrefix'], $file['fvFilename']);
			$image['imgHeight'] = $file['value'];
			if ($maxHeight == 0 || $file['value'] > $maxHeight) {
				$maxHeight = $file['value'];
			}
			$images[] = $image;
		}
		$this->images = $images;
	}

	function loadImages(){
		if(intval($this->bID)==0) $this->images=array();
		$sortChoices=array('ORDER'=>'position','RANDOM-SET'=>'groupSet asc, position asc','RANDOM'=>'rand()');
		if( !array_key_exists($this->playback,$sortChoices) ) 
			$this->playback='ORDER';
		if(intval($this->bID)==0) return array();
		$sql = "SELECT * FROM btSlideshowImg WHERE bID=".intval($this->bID).' ORDER BY '.$sortChoices[$this->playback];
		$this->images=$this->db->getAll($sql); 
	}
	
	function delete(){
		$this->db->query("DELETE FROM btSlideshowImg WHERE bID=".intval($this->bID));		
		parent::delete();
	}
	
	function save($data) { 
		$args['playback'] = isset($data['playback']) ? trim($data['playback']) : 'ORDER';
		
		if( $data['type'] == 'FILESET' && $data['fsID'] > 0){
			$args['fsID'] = $data['fsID'];
			$args['duration'] = $data['duration'][0];
			$args['fadeDuration'] = $data['fadeDuration'][0];

			$files = $this->db->getAll("SELECT fv.fID FROM FileSetFiles fsf, FileVersions fv WHERE fsf.fsID = " . $data['fsID'] .
			         " AND fsf.fID = fv.fID AND fvIsApproved = 1");
			
			//delete existing images
			$this->db->query("DELETE FROM btSlideshowImg WHERE bID=".intval($this->bID));
		} else if( $data['type'] == 'CUSTOM' && count($data['imgFIDs']) ){
			$args['fsID'] = 0;

			//delete existing images
			$this->db->query("DELETE FROM btSlideshowImg WHERE bID=".intval($this->bID));
			
			//loop through and add the images
			$pos=0;
			foreach($data['imgFIDs'] as $imgFID){ 
				if(intval($imgFID)==0 || $data['fileNames'][$pos]=='tempFilename') continue;
				$vals = array(intval($this->bID),intval($imgFID), trim($data['url'][$pos]),intval($data['duration'][$pos]),intval($data['fadeDuration'][$pos]),
						intval($data['groupSet'][$pos]),intval($data['imgHeight'][$pos]),$pos);
				$this->db->query("INSERT INTO btSlideshowImg (bID,fID,url,duration,fadeDuration,groupSet,imgHeight,position) values (?,?,?,?,?,?,?,?)",$vals);
				$pos++;
			}
		}
		
		parent::save($args);
	}
	
	function randomizeImages()
	{
		if($this->playback == 'RANDOM')
		{
			shuffle($this->images);
		}
		else if($this->playback == 'RANDOM-SET')
		{
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
	}
}

?>
