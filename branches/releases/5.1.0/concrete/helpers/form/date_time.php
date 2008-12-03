<?php 
/**
 * @package Helpers
 * @category Concrete
 * @subpackage DateTime
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Special form elements for date and time items. These can include calendars and time fields automatically.
 * @package Helpers
 * @category Concrete
 * @subpackage DateTime
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die(_("Access Denied."));
class FormDateTimeHelper {

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
			$dt = date('Y-m-d', strtotime($arr[$field . '_dt']));
			$str = $dt . ' ' . $arr[$field . '_h'] . ':' . $arr[$field . '_m'] . ' ' . $arr[$field . '_a'];
			return date('Y-m-d H:i:s', strtotime($str));
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
	 */
	public function datetime($prefix, $value = null, $includeActivation = false) {
		if ($value != null) {
			$dt = date('m/d/Y', strtotime($value));
			$h = date('h', strtotime($value));
			$m = date('i', strtotime($value));
			$a = date('A', strtotime($value));
		} else {
			$dt = date('m/d/Y');
			$h = date('h');
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
			
			$html .= '<input type="checkbox" id="' . $id . '_activate" class="ccm-activate-date-time" ccm-date-time-id="' . $id . '" name="' . $prefix . '_activate" ' . $activated . ' />';
		}
		$html .= '<span class="ccm-input-date-wrapper" id="' . $id . '_dw"><input id="' . $id . '_dt" name="' . $prefix . '_dt" class="ccm-input-date" value="' . $dt . '" ' . $disabled . ' /></span>';
		$html .= '<span class="ccm-input-time-wrapper" id="' . $id . '_tw">';
		$html .= '<select id="' . $id . '_h" name="' . $prefix . '_h" ' . $disabled . '>';
		for ($i = 1; $i <= 12; $i++) {
			if ($h == $i) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$html .= '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
		}
		$html .= '</select>:';
		$html .= '<select id="' . $id . '_m" name="' . $prefix . '_m" ' . $disabled . '>';
		for ($i = 0; $i <= 59; $i++) {
			if ($m == sprintf('%02d', $i)) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$html .= '<option value="' . sprintf('%02d', $i) . '"' . $selected . '>' . sprintf('%02d', $i) . '</option>';
		}
		$html .= '</select>';
		$html .= '<select id="' . $id . '_a" name="' . $prefix . '_a" ' . $disabled . '>';
		$html .= '<option value="AM" ';
		if ($a == 'AM') {
			$html .= 'selected';
		}
		$html .= '>AM</option>';
		$html .= '<option value="PM" ';
		if ($a == 'PM') {
			$html .= 'selected';
		}
		$html .= '>PM</option>';
		$html .= '</select>';
		$html .= '</span>';
		$html .= '<script type="text/javascript">$(function() { $("#' . $id . '_dt").datepicker({ showAnim: \'fadeIn\' }); });</script>';
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

}

