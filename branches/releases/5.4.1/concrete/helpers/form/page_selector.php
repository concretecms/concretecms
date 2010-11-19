<?php 
/**
 * @package Helpers
 * @category Concrete
 * @subpackage Forms
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Special form elements for choosing a page from the concrete5 sitemap tool.
 * @package Helpers
 * @category Concrete
 * @subpackage Forms
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class FormPageSelectorHelper {

	
	/** 
	 * Creates form fields and JavaScript page chooser for choosing a page. For use with inclusion in blocks.
	 * <code>
	 *     $dh->selectPage('pageID', '1'); // prints out the home page and makes it selectable. 
	 * </code>
	 * @param int $cID
	 */
	 
	public function selectPage($fieldName, $cID = false, $javascriptFunc='ccm_selectSitemapNode') {
		$selectedCID = 0;
		if (isset($_REQUEST[$fieldName])) {
			$selectedCID = $_REQUEST[$fieldName];
		} else if ($cID > 0) {
			$selectedCID = $cID;
		}

		$html = '';
		$html .= '<div class="ccm-summary-selected-item"><div class="ccm-summary-selected-item-inner"><strong class="ccm-summary-selected-item-label">';
		if ($selectedCID > 0) {
			$oc = Page::getByID($selectedCID);
			$html .= $oc->getCollectionName();
		}
		$html .= '</strong></div>';
		$html .= '<a class="ccm-sitemap-select-page" dialog-sender="' . $fieldName . '" dialog-width="90%" dialog-height="70%" dialog-modal="false" dialog-title="' . t('Choose Page') . '" href="' . REL_DIR_FILES_TOOLS_REQUIRED . '/sitemap_search_selector.php?sitemap_select_mode=select_page&cID=' . $selectedCID . '">' . t('Select Page') . '</a>';		$html .= '<input type="hidden" name="' . $fieldName . '" value="' . $selectedCID . '">';
		$html .= '</div>'; 
		$html .= '<script type="text/javascript"> 
		var ccmActivePageField;
		$(function() {
			$("a.ccm-sitemap-select-page").unbind();
			$("a.ccm-sitemap-select-page").dialog();
			$("a.ccm-sitemap-select-page").click(function() {
				ccmActivePageField = this;
			});
		});
		ccm_selectSitemapNode = function(cID, cName) { ';
		if($javascriptFunc=='' || $javascriptFunc=='ccm_selectSitemapNode'){
			$html .= '
			var fieldName = $(ccmActivePageField).attr("dialog-sender");
			var par = $(ccmActivePageField).parent().find(\'.ccm-summary-selected-item-label\');
			var pari = $(ccmActivePageField).parent().find("[name=\'"+fieldName+"\']");
			par.html(cName);
			pari.val(cID);
			';
		}else{
			$html .= $javascriptFunc."(cID, cName); \n";
		}
		$html .= "} \r\n </script>";
		return $html;
	}
	
	/* Embed a sitemap in javascript dialog.  Supports the following args:
     *  'node_action'   - path to script containing code to be execute when user clicks on a node in the sitemap
     *  'dialog_title'  - dialog title
     *  'dialog_height' - dialog height (default: 350px) 
     *  'dialog_width'  - dialog width (default: 350px)
     *  'target_id'     - id of the (hidden) field on the parent page that is to receive the CID of the chosen page
	 *                    (do not include the '#')
     *  (any other arguments the dashboard/sitemap element supports)
	 */
	public function sitemap($args) {
		if (!isset($args['select_mode'])) {
			$args['select_mode'] = 'move_copy_delete';
		}
		if (empty($args['node_action'])) {
			$args['node_action'] = '<none>';
		}
		if (empty($args['display_mode'])) {
			$args['display_mode'] = 'full';
		}
		if (empty($args['instance_id'])) {
			$args['instance_id'] = time();
		}
    	Loader::element('dashboard/sitemap', $args);
	}
	
}
