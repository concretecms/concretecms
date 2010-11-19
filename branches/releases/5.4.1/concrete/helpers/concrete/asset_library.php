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

	defined('C5_EXECUTE') or die("Access Denied.");
	class ConcreteAssetLibraryHelper {
	
		/** 
		 * Sets up a file field for use with a block. 
		 * @param string $id The ID of your form field
		 * @param string $postname The name of your database column into which you'd like to save the file ID
		 * @param string $chooseText
		 * @param LibraryFileBlock $bf
		 * return string $html
		 */
		public function file($id, $postname, $chooseText, $bf = null, $filterArgs = array()) {
		
			$selectedDisplay = 'none';
			$resetDisplay = 'block';
			$fileID = 0;
			
			if (isset($_POST[$postname])) {
				$bf = File::getByID($_POST[$postname]);
			}
			
			if (is_object($bf) && (!$bf->isError()) && $bf->getFileID() > 0) {
				$fileID = $bf->getFileID();
				$selectedDisplay = 'block';
				$resetDisplay = 'none';
			}
				
			$html = '<div id="' . $id . '-fm-selected" class="ccm-file-selected-wrapper" style="display: ' . $selectedDisplay . '"><img src="' . ASSETS_URL_IMAGES . '/throbber_white_16.gif" /></div>';
			
			$html .= '<div class="ccm-file-manager-select" id="' . $id . '-fm-display" ccm-file-manager-field="' . $id . '" style="display: ' . $resetDisplay . '">';
			$html .= '<a href="javascript:void(0)" onclick="ccm_chooseAsset=false; ccm_alLaunchSelectorFileManager(\'' . $id . '\')">' . $chooseText . '</a>';
			if ($filterArgs != false) {
				foreach($filterArgs as $key => $value) {
					$html .= '<input type="hidden" class="ccm-file-manager-filter" name="' . $key . '" value="' . $value . '" />';
				}
			}
			$html .= '</div><input id="' . $id . '-fm-value" type="hidden" name="' . $postname . '" value="' . $fileID . '" />';

			if (is_object($bf) && (!$bf->isError()) && $bf->getFileID() > 0) {
				$html .= '<script type="text/javascript">$(function() { ccm_triggerSelectFile(' . $fileID . ', \'' . $id . '\'); });</script>';
			}
			
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
			$args = array();
			$args['fType'] = FileType::T_IMAGE;
			return $this->file($id, $postname, $chooseText, $fileInstanceBlock, $args);
		}
	
	}
	
?>