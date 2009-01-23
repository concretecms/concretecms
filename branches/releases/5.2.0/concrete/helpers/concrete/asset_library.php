<?php 
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions useful for adding asset library access to your blocks and applications.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

	defined('C5_EXECUTE') or die(_("Access Denied."));
	class ConcreteAssetLibraryHelper {
	
		/** 
		 * Sets up a file field for use with a block. 
		 * @param string $id The ID of your form field
		 * @param string $postname The name of your database column into which you'd like to save the file ID
		 * @param string $chooseText
		 * @param LibraryFileBlock $bf
		 * return string $html
		 */
		public function file($id, $postname, $chooseText, $bf = null) {
		
			// id = the id prefix of the various items  - this is just a unique name
			// postname = the name your script expects to carry the filename
			
			$fileID = "";
			$filename = t("None selected.");
			$resetText = t('Reset');
			$filedisplay = 'inline';
			$resetdisplay = 'none';
			
			
			if ($bf != null) {
				$fileID = $bf->getFileID();
				$filename = $bf->getFilename();
				$filedisplay = 'none';
				$resetdisplay = 'inline';
			}
			$html = '<div id="' . $id . '-display" style="display: inline">' . $filename . '</div> ';
			$html .= '<a class="ccm-launch-al" id="' . $id . '" href="#" style="display: ' . $filedisplay . '">' . $chooseText . '</a> ';
			$html .= '<a class="ccm-reset-al" id="' . $id . '-reset" href="#" style="display: ' . $resetdisplay . '">' . $resetText . '</a> ';
			$html .= '<input id="' . $id . '-value" type="hidden" name="' . $postname . '" value="' . $fileID . '" />';
			
			return $html;
		}
		
		/** 
		 * Sets up an image to be chosen for use with a block.
		 * @param string $id The ID of your form field
		 * @param string $postname The name of your database column into which you'd like to save the file ID
		 * @param string $chooseText
		 * @param LibraryFileBlock $bf
		 * return string $html
		 */
		public function image($id, $postname, $chooseText, $fileInstanceBlock = null) {
			return $this->file($id, $postname, $chooseText, $fileInstanceBlock);
		}
	
	}
	
?>