<?
	require_once(DIR_FILES_BLOCK_TYPES_CORE . '/library_file/controller.php');
	
	class FileBlockController extends BlockController {

		protected $btDescription = "Creates links to files";
		protected $btName = "File";
		protected $btInterfaceWidth = 300;
		protected $btInterfaceHeight = 200;
		protected $btTable = 'btContentFile';

		function getFileID() {return $this->fID;}
		function getFileObject() {
			return LibraryFileBlockController::getFile($this->fID);
		}		
		function getLinkText() {return $this->fileLinkText;}
		
		function delete() {
			LibraryFileBlockController::delete($this->fID);
			parent::delete();
		}
		
	}
?>