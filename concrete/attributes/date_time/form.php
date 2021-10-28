<?php

use Concrete\Core\Support\Facade\Facade;

defined('C5_EXECUTE') or die('Access Denied.');
$app = Facade::getFacadeApplication();
switch ($displayMode) {
    case 'text':
    case 'date_text':
        if ($textCustomFormat !== '') {
            $format = null;
            try {
                $format = $textCustomFormat;
                $test = $date->formatCustom($format, 'now');
            } catch (\Punic\Exception $e) {
                // In case the user enters something invalid like MM/DD/YY
                unset($format);
            }
        }
        if (!isset($format)) {
            if ($displayMode === 'date_text') {
                $format = $date->getPHPDatePattern();
            } else {
                $format = $date->getPHPDateTimePattern();
            }
        }
        if ($value === null) {
            $placeholder = $date->formatCustom($format, 'now');
        } else {
            $value = $date->formatCustom($format, $value);
            $placeholder = $value;
        }
        $form = $app->make('helper/form');
        echo $form->text($view->field('value'), $value, ['placeholder' => $placeholder]);
        break;
    case 'date':
        echo $form_date_time->date($view->field('value'), $value);
        break;
    default:
        echo $form_date_time->datetime($view->field('value'), $value, false, true, null, $timeResolution);
        break;
}

$timezone = $date->getUserTimeZoneID();
if ($timezone) {
    $timezone = $date->getTimezoneDisplayName($timezone);
    echo '<div class="text-muted"><small>' . $timezone . '</small></div>';
}
