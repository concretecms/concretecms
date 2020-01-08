<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="form-group">
    <?=$form->label('calendarID', t('Calendar'))?>
    <?=$form->select('calendarID', $calendars, $calendarID);?>
</div>

