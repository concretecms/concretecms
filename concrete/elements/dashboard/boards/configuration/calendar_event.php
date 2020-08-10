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

<h3 class="font-weight-light"><?=t('Filters')?></h3>
<p><small class="text-muted"><?=t("Add search fields below to limit the pages added.")?></small></p>
<?php
$fieldSelector->render();
?>
