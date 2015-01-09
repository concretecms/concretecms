<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-ui">
<form method="post" data-dialog-form="event-form" class="ccm-event-add form-horizontal" action="<?=$controller->action('submit')?>">
    <? View::element('calendar/event/form');?>
</form>
</div>
<div class="dialog-buttons">
    <button class="btn btn-default pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
    <button type="button" data-dialog-action="submit" class="btn btn-primary pull-right"><?=t('Add Event')?></button>
</div>
