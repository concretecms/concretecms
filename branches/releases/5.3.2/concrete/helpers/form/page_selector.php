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

defined('C5_EXECUTE') or die(_("Access Denied."));
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
		$html .= '<div class="ccm-summary-selected-page"><div class="ccm-summary-selected-page-inner"><strong class="ccm-summary-selected-page-label">';
		if ($selectedCID > 0) {
			$oc = Page::getByID($selectedCID);
			$html .= $oc->getCollectionName();
		}
		$html .= '</strong></div>';
		$html .= '<a class="ccm-sitemap-select-page dialog-launch" onclick="ccmActivePageField=this" dialog-width="600" dialog-height="450" dialog-modal="false" dialog-title="' . t('Choose Page') . '" href="' . REL_DIR_FILES_TOOLS_REQUIRED . '/sitemap_overlay.php?sitemap_mode=select_page">' . t('Select Page') . '</a>';
		$html .= '<input type="hidden" name="' . $fieldName . '" value="' . $selectedCID . '">';
		$html .= '</div>'; 
		$html .= '<script type="text/javascript"> 
		ccm_selectSitemapNode = function(cID, cName) { ';
		if($javascriptFunc=='' || $javascriptFunc=='ccm_selectSitemapNode'){
			$html .= '
			var par = $(ccmActivePageField).parent().find(\'.ccm-summary-selected-page-label\');
			var pari = $(ccmActivePageField).parent().find(\'[name=' . $fieldName . ']\');
			par.html(cName);
			pari.val(cID);
			';
		}else{
			$html .= $javascriptFunc."(cID, cName); \n";
		}
		$html .= "} \r\n </script>";
		return $html;
	}
	
	
}