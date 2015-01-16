<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-ui">
<form method="post" data-dialog-form="event-form" class="ccm-event-add form-stacked" action="<?= $controller->action('submit') ?>">
    <?php
    if ($occurrence) {
        ?>
        <input type="hidden" name="occurrence_id" value="<?= $occurrence->getID() ?>" />
        <?php
    }
    View::element('calendar/event/form', array('occurrence' => $occurrence ?: null));
    ?>
</form>
</div>
<div class="dialog-buttons">
    <button class="btn btn-default pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>

    <button type="button" data-dialog-action="submit" class="btn btn-primary pull-right">
        <?= $occurrence ? t('Save Event') : t('Add Event') ?>
    </button>

    <?php
    if ($occurrence) {
        ?>
        <a href="<?= $controller->action('delete_local') ?>" class="btn pull-right btn-danger delete-local" style="display:none;margin: .5em .4em .5em 0;cursor: pointer;">
            <?= t('Delete') ?>
        </a>
        <a href="<?= $controller->action('delete') ?>" class="btn pull-right btn-danger delete-all" style="margin: .5em .4em .5em 0;cursor: pointer;">
            <?= t('Delete') ?>
        </a>
        <?php /*
        <a href="<?= $controller->action('cancel') ?>" class="btn pull-right btn-warning" style="margin: .5em .4em .5em 0;cursor: pointer;">
            <?= t('Cancel Occurrence') ?>
        </a>
           */ ?>
    <?php
    }
    ?>
</div>
