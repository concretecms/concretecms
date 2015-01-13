<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Core::make('helper/form');
$color = Core::make('helper/form/color');
?>

<form method="post" action="<?=$view->action('submit')?>">
    <?=Loader::helper('validation/token')->output('submit')?>
    <div class="form-group">
        <?=$form->label('calendarName', t('Calendar Name'))?>
        <?=$form->text('calendarName')?>
    </div>

    <div class="form-group">
        <?=$form->label('caColor', t('Event Color'))?>
        <div>
        <?=$color->output('caColor', '#3988ED')?>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions ">
            <a href="<?=$view->url('/dashboard/calendar/events')?>" class="btn btn-default pull-left"><?=t("Cancel")?></a>
            <button type="submit" class="btn btn-primary pull-right"><?=t('Add Calendar')?></button>
        </div>
    </div>
</form>