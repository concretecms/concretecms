<?
	Loader::block('library_file');
	defined('C5_EXECUTE') or die(_("Access Denied."));	
	class ImageBlockController extends BlockController {

		protected $btDescription = "Adds images and onstates from the library to pages.";
		protected $btName = "Image";
		protected $btInterfaceWidth = 300;
		protected $btInterfaceHeight = 250;
		protected $btTable = 'btContentImage';
	
		function getFileID() {return $this->fID;}
		function getFileOnstateID() {return $this->fOnstateID;}
		function getFileOnstateObject() {
			return LibraryFileBlockController::getFile($this->fOnstateID);
		}
		function getFileObject() {
			return LibraryFileBlockController::getFile($this->fID);
		}		
		function getAltText() {return $this->altText;}
		
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
			// we now pipe all files through dl_file.php ...
			//$relPath = REL_DIR_FILES_TOOLS . '/dl_file.php?bID=' . $bID . '&cID=' . $c->getCollectionID();

			$img = "<img border=\"0\" alt=\"{$this->altText}\" src=\"{$relPath}\" {$size[3]} ";
			$img .= ($align) ? "align=\"{$align}\" " : '';
			$img .= ($style) ? "style=\"{$style}\" " : '';
			$img .= ($id) ? "id=\"{$id}\" " : "";
			$img .= "/>";

			return $img;
		}

	}

?>