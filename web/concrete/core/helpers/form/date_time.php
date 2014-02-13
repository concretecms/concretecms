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
 * Special form elements for date and time items. These can include calendars and time fields automatically.
 * @package Helpers
 * @category Concrete
 * @subpackage Forms
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Helper_Form_DateTime {

	/** 
	 * Takes a "field" and grabs all the corresponding disparate fields from $_POST and translates into a timestamp
	 * @param string $field
	 * @param array $arr
	 * @return string $dateTime
	 */
	public function translate($field, $arr = null) {
		if ($arr == null) {
			$arr = $_POST;
		}
		if (isset($arr[$field . '_dt'])) {
            		if ($arr[$field . '_dt'] == '') {
                		return '';
			}
			// Timestamp is in ms - so "/ 1000" is needed
			$dt = date('Y-m-d', floor( $arr[$field . '_dt'] / 1000) );
            if (DATE_FORM_HELPER_FORMAT_HOUR == '12') {
				$str = $dt . ' ' . $arr[$field . '_h'] . ':' . $arr[$field . '_m'] . ' ' . $arr[$field . '_a'];
			} else {
				$str = $dt . ' ' . $arr[$field . '_h'] . ':' . $arr[$field . '_m'];
			}
			return date('Y-m-d H:i:s', strtotime($str));
		} else if (isset($arr[$field])) {
            		if ($arr[$field] == '') {
                		return '';
			}
			$dt = date('Y-m-d', floor( $arr[$field] / 1000) );
			return $dt;
		} else {
			return false;
		}
	}

	/** 
	 * Creates form fields and JavaScript calendar includes for a particular item
	 * <code>
	 *     $dh->datetime('yourStartDate', '2008-07-12 3:00:00');
	 * </code>
	 * @param string $prefix
	 * @param string $value
	 * @param bool $includeActivation
	 * @param bool $calendarAutoStart
	 */
	public function datetime($prefix, $value = null, $includeActivation = false, $calendarAutoStart = true) {
		if (substr($prefix, -1) == ']') {
			$prefix = substr($prefix, 0, strlen($prefix) -1);
			$_activate = $prefix . '_activate]';
			$_dt = $prefix . '_dt]';
			$_h = $prefix . '_h]';
			$_m = $prefix . '_m]';
			$_a = $prefix . '_a]';
		} else {
			$_activate = $prefix . '_activate';
			$_dt = $prefix . '_dt';
			$_h = $prefix . '_h';
			$_m = $prefix . '_m';
			$_a = $prefix . '_a';
		}
		
		$dfh = (DATE_FORM_HELPER_FORMAT_HOUR == '12') ? 'h' : 'H';
		$dfhe = (DATE_FORM_HELPER_FORMAT_HOUR == '12') ? '12' : '23';
		$dfhs = (DATE_FORM_HELPER_FORMAT_HOUR == '12') ? '1' : '0';
		if ($value != null) {
			$defaultDateJs = 'new Date(' . strtotime($value) * 1000 . ')';
			$h = date($dfh, strtotime($value));
			$m = date('i', strtotime($value));
			$a = date('A', strtotime($value));
		} else {
			$defaultDateJs = "new Date()";
			$h = date($dfh);
			$m = date('i');
			$a = date('A');
		}
		$id = preg_replace("/[^0-9A-Za-z-]/", "_", $prefix);
		$html = '';
		$disabled = false;
		if ($includeActivation) {
			if ($value) {
				$activated = 'checked';
			} else {
				$disabled = 'disabled';
			}
			
			$html .= '<input type="checkbox" id="' . $id . '_activate" class="ccm-activate-date-time" ccm-date-time-id="' . $id . '" name="' . $_activate . '" ' . $activated . ' />';
		}
		$html .= '<span class="ccm-input-date-wrapper" id="' . $id . '_dw"><input id="' . $id . '_dt_pub" name="' . $_dt . '_pub" class="ccm-input-date"  ' . $disabled . ' /><input id="' . $id . '_dt" name="' . $_dt . '" type="hidden" ' . $disabled . ' /></span>';
		$html .= '<span class="ccm-input-time-wrapper" id="' . $id . '_tw">';
		$html .= '<select id="' . $id . '_h" name="' . $_h . '" ' . $disabled . '>';
		for ($i = $dfhs; $i <= $dfhe; $i++) {
			if ($h == $i) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$html .= '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
		}
		$html .= '</select>:';
		$html .= '<select id="' . $id . '_m" name="' . $_m . '" ' . $disabled . '>';
		for ($i = 0; $i <= 59; $i++) {
			if ($m == sprintf('%02d', $i)) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$html .= '<option value="' . sprintf('%02d', $i) . '" ' . $selected . '>' . sprintf('%02d', $i) . '</option>';
		}
		$html .= '</select>';
		if (DATE_FORM_HELPER_FORMAT_HOUR == '12') {
			$html .= '<select id="' . $id . '_a" name="' . $_a . '" ' . $disabled . '>';
			$html .= '<option value="AM" ';
			if ($a == 'AM') {
				$html .= 'selected';
			}
			$html .= '>';
			// This prints out the translation of "AM" in the current language
			$html .= Loader::helper("date")->date("A",mktime(1));
			$html .= '</option>';
			$html .= '<option value="PM" ';
			if ($a == 'PM') {
				$html .= 'selected';
			}
			$html .= '>';
			// This prints out the translation of "PM" in the current language
			$html .= Loader::helper("date")->date("A",mktime(13));
			$html .= '</option>';
			$html .= '</select>';
		}
		$html .= '</span>';
		if ($calendarAutoStart) { 
			$html .= '<script type="text/javascript">$(function() { $("#' . $id . '_dt_pub").datepicker({ dateFormat: \'' . DATE_APP_DATE_PICKER . '\', altFormat: "@", altField: "#' . $id . '_dt", changeYear: true, showAnim: \'fadeIn\' }).datepicker( "setDate" , ' . $defaultDateJs . ' ) })</script>';
		}
		// first we add a calendar input
		
		if ($includeActivation) {
			$html .=<<<EOS
			<script type="text/javascript">$("#{$id}_activate").click(function() {
				if ($(this).get(0).checked) {
					$("#{$id}_dw input").each(function() {
						$(this).get(0).disabled = false;
					});
					$("#{$id}_tw select").each(function() {
						$(this).get(0).disabled = false;
					});
				} else {
					$("#{$id}_dw input").each(function() {
						$(this).get(0).disabled = true;
					});
					$("#{$id}_tw select").each(function() {
						$(this).get(0).disabled = true;
					});
				}
			});
			</script>
EOS;
			
		}
		return $html;
	
	}
	
	/** 
	 * Creates form fields and JavaScript calendar includes for a particular item but includes only calendar controls (no time.)
	 * <code>
	 *     $dh->date('yourStartDate', '2008-07-12 3:00:00');
	 * </code>
	 * @param string $prefix
	 * @param string $value
	 * @param bool $includeActivation
	 * @param bool $calendarAutoStart
	 */
	public function date($field, $value = null, $calendarAutoStart = true) {
		$id = preg_replace("/[^0-9A-Za-z-]/", "_", $field);
		if (isset($_REQUEST[$field])) {
			$defaultDateJs = 'new Date(' . $_REQUEST[$field] .')' ;
		} else if ($value != "") {
			$defaultDateJs = 'new Date(' . (int) strtotime($value) * 1000 . ')';
		} else if ($value === '') {
			$defaultDateJs = '""';
		} else {
			$defaultDateJs = 'new Date()';
		}
		//$id = preg_replace("/[^0-9A-Za-z-]/", "_", $prefix);
		$html = '';
		$html .= '<span class="ccm-input-date-wrapper" id="' . $id . '_dw"><input id="' . $id . '_pub" name="' . $field . '_pub" class="ccm-input-date"  /><input id="' . $id . '" name="' . $field . '" type="hidden"  /></span>';

		if ($calendarAutoStart) { 
			$html .= '<script type="text/javascript">$(function() { $("#' . $id . '_pub").datepicker({ dateFormat: \'' . DATE_APP_DATE_PICKER . '\', altFormat: "@", altField: "#' . $id . '", changeYear: true, showAnim: \'fadeIn\' }).datepicker( "setDate" , ' . $defaultDateJs . ' ); });</script>';
		}
		return $html;
	
	}	

}

