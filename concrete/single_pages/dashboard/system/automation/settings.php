<?php

defined('C5_EXECUTE') or die("Access Denied."); ?>

<form method="post" action="<?= $view->action('submit') ?>">
    <div class="form-group">
        <label class="control-label">
            <?= t('Queue Listening') ?>
        </label>
        <div class="form-check">
            <?= $form->radio('listening', 'app', $listening === 'app') ?>
            <label class="form-check-label" for="listening1"><?= t(
                    'Automatic - Works without advanced configuration.'
                ) ?></label>
        </div>
        <div class="form-check">
            <?= $form->radio('listening', 'worker', $listening === 'worker') ?>
            <label class="form-check-label" for="listening2"><?= t(
                    'Manual - More immediate and efficient, but requires command line access.'
                ) ?></label>
        </div>
    </div>
    <div class="help-block"><?= t(
            'If set to automatic, queued actions like file rescans and bulk page deletions be performed when triggered, but may abort if leaving a page. Want to improve their efficiency and/or ensure they run in the background? Enable manual queue processing.'
        ) ?></div>
    <div class="alert alert-warning" data-notice="listener" style="display: none"><?= t(
            'If you enable manual listening you <b>must</b> ensure the queue listener is running at least one worker: <code>concrete/bin/concrete5 messenger:consume</code>'
        ); ?></div>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="float-left btn btn-secondary" id="ccm-editor-preview-toggle"><?= t('Preview') ?></button>
            <button class="float-right btn btn-primary" type="submit"><?= t('Save') ?></button>
        </div>
    </div>
    <?php
    $token->output('submit') ?>
</form>

<script type="text/javascript">
    $(function () {
        $('input[name=listening]').on('change', function () {
            if ($('#listening2').is(':checked')) {
                $('div[data-notice=listener]').show();
            } else {
                $('div[data-notice=listener]').hide();
            }
        }).trigger('change');
    });
</script>