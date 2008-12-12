<?
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::block('library_file');

class FlickrBlockController extends BlockController {
	
	var $pobj;

	protected $btTable = 'btFlickr';
	protected $btInterfaceWidth = "400";
	protected $btInterfaceHeight = "170";
	
	public $itemsToDisplay = "5";  
	public $title = "";	
	public $maxWidth = "200";  
	public $maxHeight = "200";  	
	
	/** 
	 * Used for localization. If we want to localize the name/description we have to include this
	 */
	public function getBlockTypeDescription() {
		return t("Display photos from the website Flickr.com");
	}
	
	public function getBlockTypeName() {
		return t("Flickr Photos");
	}		
	
	public function getJavaScriptStrings() {
		return array(
			'feed-address' => t('Please enter a valid feed address.'),
			'feed-num-items' => t('Please enter the number of items to display.')
		);
	}
	
	function __construct($obj = null) {		
		parent::__construct($obj); 
	}
	
	function view(){ 
		$fp = Loader::helper("feed");			
		$feed = $fp->load($this->url); 
		$feed->set_item_limit( intval($this->itemsToDisplay) );
		$feed->init();
		$feed->handle_content_type();
		$posts = $feed->get_items();
		
		if( $feed->error() )
			$this->set('errorMsg', t("Oops, it looks like you've entered an invalid feed address!") );
			
		$this->set('posts', $posts);
		$this->set('title', $this->title);
		$this->set('maxWidth', intval($this->maxWidth) );
		$this->set('maxHeight', intval($this->maxHeight) );
	}
	
	function save($data) { 
		$args['url'] = isset($data['url']) ? $data['url'] : '';
		$args['itemsToDisplay'] = (intval($data['itemsToDisplay'])>0) ? intval($data['itemsToDisplay']) : 5 ; 
		$args['title'] = isset($data['title']) ? $data['title'] : '';
		$args['maxWidth'] = intval($data['maxWidth']) ;
		$args['maxHeight'] = intval($data['maxHeight']) ;
		parent::save($args);
	}
	
	public function getResizedImagePath($fileURL='',$img_id=''){ 		
		//get file extension	 
		if(strrpos($fileURL,'?')) $fileURL=substr($fileURL,0,strpos($fileURL,'?'));
		if(strrpos($fileURL,'.')) $file_ext=substr($fileURL,strrpos($fileURL,'.'));	
		
		$photo_key=md5(strtolower($img_id)); 
		$fileCacheName='flickr_cached_img_'.$photo_key.$file_ext;
		$fileCachePathRel=REL_DIR_FILES_CACHE.'/'.$fileCacheName;	
		
		//copy the original image locally
		if( !file_exists(DIR_FILES_CACHE.'/'.$fileCacheName) ){			
			$this->copyImgToLocal($fileURL, DIR_FILES_CACHE.'/', $fileCacheName );	 
		}
		if( !file_exists(DIR_FILES_CACHE.'/'.$fileCacheName) ) return false;
		
		//resize the image
		if(intval($this->maxWidth)>0 && intval($this->maxHeight)>0){
			$resizedFileCacheName='flickr_cached_img_'.$photo_key.'_'.$this->maxWidth.'x'.$this->maxHeight.$file_ext;
			$fileCachePathRel=REL_DIR_FILES_CACHE.'/'.$resizedFileCacheName;		
			if( !file_exists(DIR_FILES_CACHE.'/'.$resizedFileCacheName) ){
				echo LibraryFileBlockController::createImage(DIR_FILES_CACHE.'/'.$fileCacheName,DIR_FILES_CACHE.'/'.$resizedFileCacheName,intval($this->maxWidth),intval($this->maxHeight));
			}
		}
		return $fileCachePathRel;
	}
	
	public function copyImgToLocal( $URL='', $upload_dir='', $filename=''){
		$tmpFile=@fopen($URL,'r');
		if(!$tmpFile) return;
		//Check Upload Directory
		if(!file_exists($upload_dir)){  
			throw new Exception("Upload Directory doesn't exist: ".$upload_dir);
			return false;					
		}
		//do the upload 
		file_put_contents($upload_dir.$filename, $tmpFile);
		fclose($tmpFile);
	}
}

?>