<?php
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $topicsKeyField \Concrete\Core\Search\Field\AttributeKeyField
 */
?>

<div class="form-group">
    <?=$form->label('calendarID', t('Calendar'))?>
    <?=$form->select('calendarID', $calendars, $calendarID);?>
</div>

<?php

if (isset($topicsKeyField)) { ?>

    <div class="form-group">
        <?=$form->label('#', t('Search by Topic'))?>
        <?=$topicsKeyField->renderSearchField();?>
    </div>

<?php }
