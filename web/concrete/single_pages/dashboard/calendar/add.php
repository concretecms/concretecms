<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Core::make('helper/form');
$color = Core::make('helper/form/color');

$calendarName = null;
$caColor = '#3988ED';
$buttonText = t('Add Calendar');
if (is_object($calendar)) {
    $calendarName = $calendar->getName();
    $caColor = $calendar->getColor();
    $buttonText = t('Save Calendar');
}
?>

<form method="post" action="<?=$view->action('submit')?>">
    <?=Loader::helper('validation/token')->output('submit')?>
    <? if (is_object($calendar)) { ?>
        <input type="hidden" name="caID" value="<?=$calendar->getID()?>" />
    <? } ?>

    <div class="form-group">
        <?=$form->label('calendarName', t('Calendar Name'))?>
        <?=$form->text('calendarName', $calendarName)?>
    </div>

    <div class="form-group">
        <?=$form->label('caColor', t('Event Color'))?>
        <div>
        <?=$color->output('caColor', $caColor)?>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions ">
            <a href="<?=$view->url('/dashboard/calendar/events')?>" class="btn btn-default pull-left"><?=t("Cancel")?></a>
            <button type="submit" class="btn btn-primary pull-right"><?=$buttonText?></button>
        </div>
    </div>
</form>