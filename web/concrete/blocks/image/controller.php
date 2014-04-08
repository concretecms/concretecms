<?
namespace Concrete\Block\Image;
use Loader;
use \File;
use \Concrete\Core\Block\BlockController;
class Controller extends BlockController {

	protected $btInterfaceWidth = 400;
	protected $btInterfaceHeight = 550;
	protected $btTable = 'btContentImage';
	protected $btCacheBlockRecord = true;
	protected $btCacheBlockOutput = true;
	protected $btCacheBlockOutputOnPost = true;
	protected $btCacheBlockOutputForRegisteredUsers = true;
	protected $btWrapperClass = 'ccm-ui';
	protected $btExportFileColumns = array('fID','fOnstateID');
	protected $btFeatures = array(
		'image'
	);

	/** 
	 * Used for localization. If we want to localize the name/description we have to include this
	 */
	public function getBlockTypeDescription() {
		return t("Adds images and onstates from the library to pages.");
	}
	
	public function getBlockTypeName() {
		return t("Image");
	}		
	
	public function view() {
		//$c = Page::getCurrentPage();
		$bID = $this->bID;
		
		$f = File::getByID($this->fID);
		$fullPath = $f->getPath();
		$relPath = $f->getRelativePath();			
		$size = @getimagesize($fullPath);
		//if (empty($size)) {
			//echo t( 'Image Not Found. ');
			 //return '';
			//maybe Log::addEntry('Image not found for area bla on page bla/bla/bla');
		//}	

		
		if ($this->maxWidth == $size[0] && $this->maxHeight == $size[1]) {
			$sizeStr = $size[3];
		} else if (!$this->forceImageToMatchDimensions && ($this->maxWidth > 0 || $this->maxHeight > 0)) { 
			$ih = Loader::helper('image');
			$thumb = $ih->getThumbnail($f, $this->maxWidth, $this->maxHeight);
			$sizeStr = ' width="' . $thumb->width . '" height="' . $thumb->height . '"';
			$relPath = $thumb->src;
		} else {
			$sizeStr = $size[3];
		}

		if($this->fOnstateID > 0) {
			$fos = File::getByID($this->fOnstateID);
			$fullPathOnstate = $f->getPath();
			$sizehover = @getimagesize($fullPathOnstate);

			if ($this->maxWidth == $sizehover[0] && $this->maxHeight == $sizehover[1]) {
				$relPathHover = $fos->getRelativePath();
			} else if (!$this->forceImageToMatchDimensions && ($this->maxWidth > 0 || $this->maxHeight > 0)) {
				$thumbHover = $ih->getThumbnail($fos, $mw, $mh);				
				$relPathHover = $thumbHover->src;
			} else {
				$relPathHover = $fos->getRelativePath();
			}

		}

		//var_dump($relPath);
		$this->set('linkURL',$this->getLinkURL());
		

		$this->set('relPath',$relPath);
		$this->set('relPathHover',$relPathHover);
		$this->set('sizeStr',$sizeStr);
		$this->set('altText',$altText);

		if($this->fOnstateID > 0) {
			$this->addHeaderItem('<style type="text/css"> img.ccm-image-block.alternate { display:none; } </style>');
		}

	}

	
	public function getJavaScriptStrings() {
		return array(
			'image-required' => t('You must select an image.')
		);
	}

	public function isComposerControlDraftValueEmpty() {
		$f = $this->getFileObject();
		if (is_object($f) && !$f->isError()) {
			return false;
		}
		return true;
	}

	public function getImageFeatureDetailFileObject() {
		// i don't know why this->fID isn't sticky in some cases, leading us to query
		// every damn time
		$db = Loader::db();
		$fID = $db->GetOne('select fID from btContentImage where bID = ?', array($this->bID));
		if ($fID) {
			$f = File::getByID($fID);
			if (is_object($f) && !$f->isError()) {
				return $f;
			}
		}
	}

	function getFileID() {return $this->fID;}
	function getFileOnstateID() {return $this->fOnstateID;}
	public function getFileOnstateObject() {
		if($this->fOnstateID) {
			return File::getByID($this->fOnstateID);
		}
	}
	public function getFileObject() {
		return File::getByID($this->fID);
	}
	function getAltText() {return $this->altText;}
	function getExternalLink() {return $this->externalLink;}
	function getInternalLinkCID() {return $this->internalLinkCID;}
	function getLinkURL() {
		if (!empty($this->externalLink)) {
			return $this->externalLink;
		} else if (!empty($this->internalLinkCID)) {
			$linkToC = Page::getByID($this->internalLinkCID);
			return (empty($linkToC) || $linkToC->error) ? '' : Loader::helper('navigation')->getLinkToCollection($linkToC);
		} else {
			return '';
		}
	}

	public function validate_composer() {
		$f = $this->getFileObject();
		$e = Loader::helper('validation/error');
		if (!is_object($f) || $f->isError() || !$f->getFileID()) {
			$e->add(t('You must specify a valid image file.'));
		}
		return $e;
	}
	
	public function save($args) {		
		$args['fID'] = ($args['fID'] != '') ? $args['fID'] : 0;
		$args['fOnstateID'] = ($args['fOnstateID'] != '') ? $args['fOnstateID'] : 0;
		$args['forceImageToMatchDimensions'] = ($args['forceImageToMatchDimensions']) ? 1 : 0;
		$args['maxWidth'] = (intval($args['maxWidth']) > 0) ? intval($args['maxWidth']) : 0;
		$args['maxHeight'] = (intval($args['maxHeight']) > 0) ? intval($args['maxHeight']) : 0;
		switch (intval($args['linkType'])) {
			case 1:
				$args['externalLink'] = '';
				break;
			case 2:
				$args['internalLinkCID'] = 0;
				break;
			default:
				$args['externalLink'] = '';
				$args['internalLinkCID'] = 0;
				break;
		}
		unset($args['linkType']); //this doesn't get saved to the database (it's only for UI usage)
		parent::save($args);
	}


	//DEPRECATED.
	public function getContentAndGenerate($align = false, $style = false, $id = null) {
		$c = Page::getCurrentPage();
		$bID = $this->bID;
		
		$f = $this->getFileObject();
		$fullPath = $f->getPath();
		$relPath = $f->getRelativePath();			
		$size = @getimagesize($fullPath);
		if (empty($size)) {
			echo t( 'Image Not Found. ');
		    return '';
		}	
		
		if ($this->maxWidth == $size[0] && $this->maxHeight == $size[1]) {
			$sizeStr = $size[3];
		} else if (!$this->forceImageToMatchDimensions && ($this->maxWidth > 0 || $this->maxHeight > 0)) { 
			$mw = $this->maxWidth > 0 ? $this->maxWidth : $size[0];
			$mh = $this->maxHeight > 0 ? $this->maxHeight : $size[1];
			$ih = Loader::helper('image');
			$thumb = $ih->getThumbnail($f, $mw, $mh);
			$sizeStr = ' width="' . $thumb->width . '" height="' . $thumb->height . '"';
			$relPath = $thumb->src;
		} else {
			$sizeStr = $size[3];
		}
		
		$img = "<img border=\"0\" class=\"ccm-image-block\" alt=\"{$this->altText}\" src=\"{$relPath}\" {$sizeStr} ";
		$img .= ($align) ? "align=\"{$align}\" " : '';
		
		$img .= ($style) ? "style=\"{$style}\" " : '';
		if($this->fOnstateID != 0) {
			$fos = $this->getFileOnstateObject();
			$fullPathOnstate = $f->getPath();
			$sizehover = @getimagesize($fullPathOnstate);

			if ($this->maxWidth == $sizehover[0] && $this->maxHeight == $sizehover[1]) {
				$relPathHover = $fos->getRelativePath();
			} else if (!$this->forceImageToMatchDimensions && ($this->maxWidth > 0 || $this->maxHeight > 0)) {
				$thumbHover = $ih->getThumbnail($fos, $mw, $mh);				
				$relPathHover = $thumbHover->src;
			} else {
				$relPathHover = $fos->getRelativePath();
			}

			$img .= " onmouseover=\"this.src = '{$relPathHover}'\" ";
			$img .= " onmouseout=\"this.src = '{$relPath}'\" ";
		}
		
		$img .= ($id) ? "id=\"{$id}\" " : "";
		$img .= "/>";
		
		$linkURL = $this->getLinkURL();
		if (!empty($linkURL)) {
			$img = "<a href=\"{$linkURL}\">" . $img ."</a>";
		}
		return $img;
	}

}

?>
