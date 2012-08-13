<?
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
class Concrete5_Helper_Form_UserSelector {

	
	/** 
	 * Creates form fields and JavaScript user chooser for choosing a user. For use with inclusion in blocks and addons.
	 * <code>
	 *     $dh->selectUser('userID', '1'); // prints out the admin user and makes it changeable.
	 * </code>
	 * @param int $uID
	 */
	
	
	public function selectUser($fieldName, $uID = false, $javascriptFunc = 'ccm_triggerSelectUser') {
		$selectedUID = 0;
		if (isset($_REQUEST[$fieldName])) {
			$selectedUID = $_REQUEST[$fieldName];
		} else if ($uID > 0) {
			$selectedUID = $uID;
		}

		$html = '';
		$html .= '<div class="ccm-summary-selected-item"><div class="ccm-summary-selected-item-inner"><strong class="ccm-summary-selected-item-label">';
		if ($selectedUID > 0) {
			$ui = UserInfo::getByID($selectedUID);
			$html .= $ui->getUserName();
		}
		$html .= '</strong></div>';
		$html .= '<a class="ccm-sitemap-select-item" id="ccm-user-selector-' . $fieldName . '" onclick="ccmActiveUserField=this" dialog-append-buttons="true" dialog-width="90%" dialog-height="70%" dialog-modal="false" dialog-title="' . t('Choose User') . '" href="' . REL_DIR_FILES_TOOLS_REQUIRED . '/users/search_dialog?mode=choose_one">' . t('Select User') . '</a>';
		$html .= '<input type="hidden" name="' . $fieldName . '" value="' . $selectedUID . '">';
		$html .= '</div>'; 
		$html .= '<script type="text/javascript">';
		$html .= '$(function() { $("#ccm-user-selector-' . $fieldName . '").dialog(); });';
		$html .= 'if (typeof(ccmActiveUserField) == "undefined") {';
		$html .= 'var ccmActiveUserField;';		
		$html .= '}';
		$html .= '
		$(function() { 
		ccm_triggerSelectUser = function(uID, uName, uEmail) { ';
		if($javascriptFunc=='' || $javascriptFunc=='ccm_triggerSelectUser'){
			$html .= '
			var par = $(ccmActiveUserField).parent().find(\'.ccm-summary-selected-item-label\');
			var pari = $(ccmActiveUserField).parent().find(\'[name=' . $fieldName . ']\');
			par.html(uName);
			pari.val(uID);
			';
		}else{
			$html .= $javascriptFunc."(uID, uName); \n";
		}
		$html .= "}}); \r\n </script>";
		return $html;
	}
	
	public function quickSelect($key, $val = false, $args = array()) {
		$form = Loader::helper('form');
		$valt = Loader::helper('validation/token');
		$token = $valt->generate('quick_user_select_' . $key);
		$html .= "
		<style type=\"text/css\">
		ul.ui-autocomplete {position:absolute; list-style:none; }
		ul.ui-autocomplete li.ui-menu-item { margin-left:0; padding:2px;}
		</style>
		<script type=\"text/javascript\">
		$(function () {
			$('#".$key."').autocomplete({source: '" . REL_DIR_FILES_TOOLS_REQUIRED . "/users/autocomplete?key=" . $key . "&token=" . $token . "'});
		} );
		</script>";
		$html .= '<span class="ccm-quick-user-selector">'.$form->text($key,$val, $args).'</span>';
		return $html;
	}
	
	public function selectMultipleUsers($fieldName) {
		
		$html = '';
		$html .= '<table id="ccmUserSelect' . $fieldName . '" class="ccm-results-list" cellspacing="0" cellpadding="0" border="0">';
		$html .= '<tr>';
		$html .= '<th>' . t('Username') . '</th>';
		$html .= '<th>' . t('Email Address') . '</th>';
		$html .= '<th><a class="ccm-user-select-item dialog-launch" onclick="ccmActiveUserField=this" dialog-append-buttons="true" dialog-width="90%" dialog-height="70%" dialog-modal="false" dialog-title="' . t('Choose User') . '" href="' . REL_DIR_FILES_TOOLS_REQUIRED . '/users/search_dialog?mode=choose_multiple"><img src="' . ASSETS_URL_IMAGES . '/icons/add.png" width="16" height="16" /></a></th>';
		$html .= '</tr><tbody id="ccmUserSelect' . $fieldName . '_body" >';
		/* for ($i = 0; $i < $ul->getTotal(); $i++ ) {
			$ui = $ul1[$i];
			$class = $i % 2 == 0 ? 'ccm-row-alt' : '';
			$html .= '<tr id="ccmUserSelect' . $fieldName . '_' . $ui->getUserID() . '" class="ccm-list-record ' . $class . '">';
			$html .= '<td><input type="hidden" name="' . $fieldName . '[]" value="' . $ui->getUserID() . '" />' . $ui->getUserName() . '</td>';
			$html .= '<td>' . $ui->getUserEmail() . '</td>';
			$html .= '<td><a href="javascript:void(0)" class="ccm-user-list-clear"><img src="' . ASSETS_URL_IMAGES . '/icons/close.png" width="16" height="16" class="ccm-user-list-clear-button" /></a>';
			$html .= '</tr>';		
		}*/
		$html .= '<tr class="ccm-user-selected-item-none"><td colspan="3">' . t('No users selected.') . '</td></tr>';
		$html .= '</tbody></table><script type="text/javascript">
		$(function() {
			$("#ccmUserSelect' . $fieldName . ' .ccm-user-select-item").dialog();
			$("a.ccm-user-list-clear").click(function() {
				$(this).parents(\'tr\').remove();
				ccm_setupGridStriping(\'ccmUserSelect' . $fieldName . '\');
			});

			ccm_triggerSelectUser = function(uID, uName, uEmail) {
				$("tr.ccm-user-selected-item-none").hide();
				if ($("#ccmUserSelect' . $fieldName . '_" + uID).length < 1) {
					var html = "";
					html += "<tr id=\"ccmUserSelect' . $fieldName . '_" + uID + "\" class=\"ccm-list-record\"><td><input type=\"hidden\" name=\"' . $fieldName . '[]\" value=\"" + uID + "\" />" + uName + "</td>";
					html += "<td>" + uEmail + "</td>";
					html += "<td><a href=\"javascript:void(0)\" class=\"ccm-user-list-clear\"><img src=\"' . ASSETS_URL_IMAGES . '/icons/close.png\" width=\"16\" height=\"16\" class=\"ccm-user-list-clear-button\" /></a>";
					html += "</tr>";
					$("#ccmUserSelect' . $fieldName . '_body").append(html);
				}
				ccm_setupGridStriping(\'ccmUserSelect' . $fieldName . '\');
				$("a.ccm-user-list-clear").click(function() {
					$(this).parents(\'tr\').remove();
					ccm_setupGridStriping(\'ccmUserSelect' . $fieldName . '\');
				});
			}
		});
		
		</script>';	
		return $html;
	}
	
	
}