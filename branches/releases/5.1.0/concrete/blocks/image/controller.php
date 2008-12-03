<?php 
	Loader::block('library_file');
	defined('C5_EXECUTE') or die(_("Access Denied."));	
	class ImageBlockController extends BlockController {

		protected $btInterfaceWidth = 300;
		protected $btInterfaceHeight = 300;
		protected $btTable = 'btContentImage';

		/** 
		 * Used for localization. If we want to localize the name/description we have to include this
		 */
		public function getBlockTypeDescription() {
			return t("Adds images and onstates from the library to pages.");
		}
		
		public function getBlockTypeName() {
			return t("Image");
		}		
		
		public function getJavaScriptStrings() {
			return array(
				'image-required' => t('You must select an image.')
			);
		}
	
	
		function getFileID() {return $this->fID;}
		function getFileOnstateID() {return $this->fOnstateID;}
		function getFileOnstateObject() {
			if ($this->fOnstateID > 0) {
				return LibraryFileBlockController::getFile($this->fOnstateID);
			}
		}
		function getFileObject() {
			return LibraryFileBlockController::getFile($this->fID);
		}		
		function getAltText() {return $this->altText;}
		function getExternalLink() {return $this->externalLink;}
		
		public function save($args) {		
			$args['fOnstateID'] = ($args['fOnstateID'] != '') ? $args['fOnstateID'] : 0;
			$args['fID'] = ($args['fID'] != '') ? $args['fID'] : 0;
			parent::save($args);
		}

		function getContentAndGenerate($align = false, $style = false, $id = null) {
			$db = Loader::db();
			global $c;
			$bID = $this->bID;
			$q = "select filename from btFile where bID = '{$this->fID}'";

			$r = $db->query($q);
			$row = $r->fetchRow();

			$fullPath = DIR_FILES_UPLOADED . '/' . $row['filename'];
			
			$size = @getimagesize($fullPath);

			$relPath = REL_DIR_FILES_UPLOADED . '/' . $row['filename'];
			
			
			$img = "<img border=\"0\" alt=\"{$this->altText}\" src=\"{$relPath}\" {$size[3]} ";
			$img .= ($align) ? "align=\"{$align}\" " : '';
			
			$img .= ($style) ? "style=\"{$style}\" " : '';
			if($this->fOnstateID != 0){
				$q = "select filename from btFile where bID = '{$this->fOnstateID}'";
	
				$r = $db->query($q);
				$row = $r->fetchRow();
				$fullPathHover = REL_DIR_FILES_UPLOADED . '/' . $row['filename'];

				$img .= " onmouseover=\"this.src = '{$fullPathHover}'\" ";
				$img .= " onmouseout=\"this.src = '{$relPath}'\" ";
			}
			
			$img .= ($id) ? "id=\"{$id}\" " : "";
			$img .= "/>";

			if($this->externalLink != ""){
				$img = "<a href=\"{$this->externalLink}\">" . $img ."</a>";
			}
			return $img;
		}

	}

?>