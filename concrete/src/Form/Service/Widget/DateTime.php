<?php
namespace Concrete\Core\Form\Service\Widget;

use Core;

class DateTime
{
    /**
     * Takes a "field" and grabs all the corresponding disparate fields from $_POST and translates into a timestamp
     * @param string $field The name of the field to translate
     * @param array $arr = null The array containing the value. If null (default) we'll use $_POST
     * @return string|false $dateTime In case of success returns the timestamp (in the format 'Y-m-d H:i:s' or 'Y-m-d'), otherwise returns false ($field value is not in the array) or '' (if $field value is empty).
     * If $field has both date and time, the resulting value will be converted fro user timezone to system timezone.
     * If $field has only date and not time, no timezone conversion will occur.
     */
    public function translate($field, $arr = null)
    {
        if ($arr == null) {
            $arr = $_POST;
        }
        $dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
        $date = null;
        if (isset($arr[$field . '_dt'])) {
            $value = $arr[$field . '_dt'];
            if (strlen(trim($value)) === 0) {
                return '';
            }
            $h = @intval($arr[$field . '_h']);
            $m = @intval($arr[$field . '_m']);
            if ($h < 12 && isset($arr[$field . '_a']) && ($arr[$field . '_a'] === 'PM')) {
                $h += 12;
            }
            $value .= ' ' . substr("0$h", -2) . ':' . substr("0$m", -2);
            try {
                $date = new \DateTime($value, $dh->getTimezone('user'));
            } catch (Exception $foo) {
            }
        } elseif (isset($arr[$field])) {
            $value = $arr[$field];
            if (strlen(trim($value)) === 0) {
                return '';
            }
            try {
                $date = new \DateTime($value, $dh->getTimezone('system'));
            } catch (Exception $foo) {
            }
        }

        return $date ? $dh->formatCustom('Y-m-d H:i:s', $date, 'system') : null;
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
            $prefix = substr($prefix, 0, strlen($prefix) - 1);
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
        list($dateYear, $dateMonth, $dateDay, $timeHour, $timeMinute) = explode(',', $dh->formatCustom('Y,n,j,G,i', $value ? $value : 'now'));
        $timeMinute = intval($timeMinute);
        if($timeFormat == 12) {
            $timeAMPM = ($timeHour < 12) ? 'AM' : 'PM';
            $timeHour = ($timeHour % 12);
            if ($timeHour == 0) {
                $timeHour = 12;
            }
        }
        if ($value === '') {
            $defaultDateJs = '""';
        } else {
            $defaultDateJs = "new Date($dateYear, $dateMonth - 1, $dateDay)";
        }
        $id = preg_replace("/[^0-9A-Za-z-]/", "_", $prefix);
        $html = '';
        $disabled = false;
        $html .= '<div class="form-inline">';

        if ($includeActivation) {
            if ($value) {
                $activated = 'checked';
            } else {
                $disabled = 'disabled';
            }
            $html .= '<input type="checkbox" id="' . $id . '_activate" class="ccm-activate-date-time" ccm-date-time-id="' . $id . '" name="' . $_activate . '" ' . $activated . ' />';
        }

        $html .= '<span class="ccm-input-date-wrapper" id="' . $id . '_dw"><input id="' . $id . '_dt_pub" class="form-control ccm-input-date"  ' . $disabled . ' /><input id="' . $id . '_dt" name="' . $_dt . '" type="hidden" ' . $disabled . ' /></span>';
        $html .= '<span class="ccm-input-time-wrapper form-inline" id="' . $id . '_tw">';
        $html .= '<select class="form-control" id="' . $id . '_h" name="' . $_h . '" ' . $disabled . '>';

        $hourStart = ($timeFormat == 12) ? 1 : 0;
        $hourEnd = ($timeFormat == 12) ? 12 : 23;
        for ($i = $hourStart; $i <= $hourEnd; $i++) {
            if ($i == $timeHour) {
                $selected = 'selected';
            } else {
                $selected = '';
            }
            $html .= '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
        }
        $html .= '</select>:';
        $html .= '<select class="form-control"  id="' . $id . '_m" name="' . $_m . '" ' . $disabled . '>';
        for ($i = 0; $i <= 59; $i++) {
            if ($i == $timeMinute) {
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
            if ($timeAMPM == 'AM') {
                $html .= 'selected';
            }
            $html .= '>';
            // This prints out the translation of "AM" in the current language
            $html .= $dh->date('A', mktime(1), 'app');
            $html .= '</option>';
            $html .= '<option value="PM" ';
            if ($timeAMPM == 'PM') {
                $html .= 'selected';
            }
            $html .= '>';
            // This prints out the translation of "PM" in the current language
            $html .= $dh->date('A', mktime(13), 'app');
            $html .= '</option>';
            $html .= '</select>';
        }
        $html .= '</span></div>';
        $jh = Core::make('helper/json'); /* @var $jh \Concrete\Core\Http\Service\Json */
        if ($calendarAutoStart) {
            $html .= '<script type="text/javascript">$(function () {
                $("#' . $id . '_dt_pub").datepicker({
                    dateFormat: ' . $jh->encode($dh->getJQueryUIDatePickerFormat(t(/*i18n: Short date format: see http://www.php.net/manual/en/function.date.php */ 'n/j/Y'))) . ',
                    altFormat: "yy-mm-dd",
                    altField: "#' . $id . '_dt",
                    changeYear: true,
                    showAnim: \'fadeIn\',
                    onClose: function(dateText, inst) {
                        if(dateText == "") {
                            var altField = $(inst.settings["altField"]);
                            if(altField.length) {
                                altField.val(dateText);
                            }
                        }
                    }
                }).datepicker("setDate" , ' . $defaultDateJs . '); })</script>';
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
        $dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
        $fh = Core::make("helper/form");
        $id = preg_replace("/[^0-9A-Za-z-]/", "_", $field);

        $requestValue = $fh->getRequestValue($field);

        if ($requestValue !== false) {
            $timestamp = empty($requestValue) ? false : @strtotime($requestValue);
        } elseif ($value) {
            $timestamp = @strtotime($value);
        } elseif ($value === '') {
            $timestamp = false;
        } else {
            // Today (in the user's timezone)
            $timestamp = strtotime($dh->formatCustom('Y-m-d'));
        }
        if ($timestamp) {
            $defaultDateJs = 'new Date(' . implode(', ', array(date('Y', $timestamp), date('n', $timestamp) - 1, date('j', $timestamp))) . ')';
        } else {
            $defaultDateJs = '""';
        }
        $html = '';
        $html .= '<div><span class="ccm-input-date-wrapper" id="' . $id . '_dw"><input id="' . $id . '_pub" type="text" class="form-control ccm-input-date"  /><input id="' . $id . '" name="' . $field . '" type="hidden"  /></span></div>';
        $jh = Core::make('helper/json'); /* @var $jh \Concrete\Core\Http\Service\Json */
        if ($calendarAutoStart) {
            $html .= '<script type="text/javascript">$(function () {
                $("#' . $id . '_pub").datepicker({
                    dateFormat: ' . $jh->encode($dh->getJQueryUIDatePickerFormat(t(/*i18n: Short date format: see http://www.php.net/manual/en/function.date.php */ 'n/j/Y'))) . ',
                    altFormat: "yy-mm-dd",
                    altField: "#' . $id . '",
                    changeYear: true,
                    showAnim: \'fadeIn\',
                    onClose: function(dateText, inst) {
                        if(dateText == "") {
                            var altField = $(inst.settings["altField"]);
                            if(altField.length) {
                                altField.val(dateText);
                            }
                        }
                    }
                }).datepicker( "setDate" , ' . $defaultDateJs . ' ); });</script>';
        }

        return $html;

    }

}
