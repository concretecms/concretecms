<?php
namespace Concrete\Core\Form\Service\Widget;

use Loader;

class DateTime
{
    /**
	 * Takes a "field" and grabs all the corresponding disparate fields from $_POST and translates into a timestamp
	 * @param string $field
	 * @param array $arr
	 * @return string $dateTime
	 */
    public function translate($field, $arr = null)
    {
        if ($arr == null) {
            $arr = $_POST;
        }
        if (isset($arr[$field . '_dt'])) {
                    if ($arr[$field . '_dt'] == '') {
                        return '';
            }
            // Timestamp is in ms - so "/ 1000" is needed
            $dt = date('Y-m-d', floor( $arr[$field . '_dt'] / 1000) );
            $dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
            if ($dh->getTimeFormat() == 12) {
                $str = $dt . ' ' . $arr[$field . '_h'] . ':' . $arr[$field . '_m'] . ' ' . $arr[$field . '_a'];
            } else {
                $str = $dt . ' ' . $arr[$field . '_h'] . ':' . $arr[$field . '_m'];
            }

            return date('Y-m-d H:i:s', strtotime($str));
        } elseif (isset($arr[$field])) {
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
    public function datetime($prefix, $value = null, $includeActivation = false, $calendarAutoStart = true)
    {
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
        
        $dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
        $timeFormat = $dh->getTimeFormat();

        $dfh = ($timeFormat == 12) ? 'h' : 'H';
        $dfhe = ($timeFormat == 12) ? '12' : '23';
        $dfhs = ($timeFormat == 12) ? '1' : '0';
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

        $html .= '<div><span class="ccm-input-date-wrapper form-inline" id="' . $id . '_dw"><input id="' . $id . '_dt_pub" name="' . $_dt . '_pub" class="form-control ccm-input-date"  ' . $disabled . ' /><input id="' . $id . '_dt" name="' . $_dt . '" type="hidden" ' . $disabled . ' /></span>';
        $html .= '<span class="ccm-input-time-wrapper form-inline" id="' . $id . '_tw">';
        $html .= '<select class="form-control" id="' . $id . '_h" name="' . $_h . '" ' . $disabled . '>';

        for ($i = $dfhs; $i <= $dfhe; $i++) {
            if ($h == $i) {
                $selected = 'selected';
            } else {
                $selected = '';
            }
            $html .= '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
        }
        $html .= '</select>:';
        $html .= '<select class="form-control"  id="' . $id . '_m" name="' . $_m . '" ' . $disabled . '>';
        for ($i = 0; $i <= 59; $i++) {
            if ($m == sprintf('%02d', $i)) {
                $selected = 'selected';
            } else {
                $selected = '';
            }
            $html .= '<option value="' . sprintf('%02d', $i) . '" ' . $selected . '>' . sprintf('%02d', $i) . '</option>';
        }
        $html .= '</select>';
        if ($timeFormat == 12) {
            $html .= '<select class="form-control" id="' . $id . '_a" name="' . $_a . '" ' . $disabled . '>';
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
        $html .= '</span></div>';
        if ($calendarAutoStart) {
            $html .= '<script type="text/javascript">$(function () { $("#' . $id . '_dt_pub").datepicker({ dateFormat: \'' . DATE_APP_DATE_PICKER . '\', altFormat: "@", altField: "#' . $id . '_dt", changeYear: true, showAnim: \'fadeIn\' }).datepicker( "setDate" , ' . $defaultDateJs . ' ) })</script>';
        }
        // first we add a calendar input

        if ($includeActivation) {
            $html .=<<<EOS
			<script type="text/javascript">$("#{$id}_activate").click(function () {
				if ($(this).get(0).checked) {
					$("#{$id}_dw input").each(function () {
						$(this).get(0).disabled = false;
					});
					$("#{$id}_tw select").each(function () {
						$(this).get(0).disabled = false;
					});
				} else {
					$("#{$id}_dw input").each(function () {
						$(this).get(0).disabled = true;
					});
					$("#{$id}_tw select").each(function () {
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
    public function date($field, $value = null, $calendarAutoStart = true)
    {
        $id = preg_replace("/[^0-9A-Za-z-]/", "_", $field);
        if (isset($_REQUEST[$field])) {
            $defaultDateJs = 'new Date(' .  preg_replace('/[^0-9]/', '', $_REQUEST[$field]) .')' ;
        } elseif ($value != "") {
            $defaultDateJs = 'new Date(' . (int) strtotime($value) * 1000 . ')';
        } elseif ($value === '') {
            $defaultDateJs = '""';
        } else {
            $defaultDateJs = 'new Date()';
        }
        //$id = preg_replace("/[^0-9A-Za-z-]/", "_", $prefix);
        $html = '';
        $html .= '<div><span class="ccm-input-date-wrapper" id="' . $id . '_dw"><input id="' . $id . '_pub" name="' . $field . '_pub" class="ccm-input-date"  /><input id="' . $id . '" name="' . $field . '" type="hidden"  /></span></div>';

        if ($calendarAutoStart) {
            $html .= '<script type="text/javascript">$(function () { $("#' . $id . '_pub").datepicker({ dateFormat: \'' . DATE_APP_DATE_PICKER . '\', altFormat: "@", altField: "#' . $id . '", changeYear: true, showAnim: \'fadeIn\' }).datepicker( "setDate" , ' . $defaultDateJs . ' ); });</script>';
        }

        return $html;

    }

}
