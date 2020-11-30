<?php

defined('C5_EXECUTE') or die("Access Denied.");

$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
//if ($request->getMethod() == 'POST') {
//    $loggingMethod = $request->request->get('loggingMethod');
//}
?>

<form method="post" action="<?= $view->action('submit') ?>" id="ccm-system-automation-settings" v-cloak>
    <div class="form-group">
        <label class="control-label">
            <?= t('Queue Listening') ?>
        </label>
        <div class="form-check">
            <?= $form->radio('listening', 'app', $listening === 'app', ['v-model' => 'listening']) ?>
            <label class="form-check-label" for="listening1"><?= t(
                    'Automatic - Works without advanced configuration.'
                ) ?></label>
        </div>
        <div class="form-check">
            <?= $form->radio('listening', 'worker', $listening === 'worker', ['v-model' => 'listening']) ?>
            <label class="form-check-label" for="listening2"><?= t(
                    'Manual - More immediate and efficient, but requires command line access.'
                ) ?></label>
        </div>
    </div>
    <div class="help-block"><?= t(
            'If set to automatic, queued actions like file rescans and bulk page deletions be performed when triggered, but may abort if leaving a page. Want to improve their efficiency and/or ensure they run in the background? Enable manual queue processing.'
        ) ?></div>
    <div class="alert alert-warning" v-show="listening === 'worker'"><?= t(
            'If you enable manual listening you <b>must</b> ensure the queue listener is running at least one worker: <code>concrete/bin/concrete5 messenger:consume</code>'
        ); ?></div>


    <div class="form-group">
        <label class="control-label">
            <?= t('Logging') ?>
        </label>
        <div class="form-check">
            <?= $form->radio('loggingMethod', 'none', $loggingMethod === 'none', ['v-model' => 'loggingMethod']) ?>
            <label class="form-check-label" for="loggingMethod3"><?= t(
                    'None - The output of task processes are not logged.'
                ) ?></label>
        </div>
        <div class="form-check">
            <?= $form->radio('loggingMethod', 'file', $loggingMethod === 'file', ['v-model' => 'loggingMethod']) ?>
            <label class="form-check-label" for="loggingMethod4"><?= t(
                    'File - A new file is logged to a directory every time a concrete5 process is run.'
                ) ?></label>
        </div>
    </div>

    <div class="form-group" v-show="loggingMethod === 'file'">
        <label class="control-label">
            <?= t('Log Directory') ?>
        </label>
        <?= $form->input('logDirectory', $logDirectory) ?>
    </div>

    <div class="alert alert-danger" v-show="loggingMethod === 'file'"><?= t(
            'If you log process output to files, ensure that they are not contained in a publicly readable directory.'
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

<script>
    $(function () {
        Concrete.Vue.activateContext('cms', function (Vue, config) {
            new Vue({
                el: '#ccm-system-automation-settings',
                data: {
                    listening: document.querySelector('input[name="listening"]:checked').value,
                    loggingMethod: document.querySelector('input[name="loggingMethod"]:checked').value,
                },
            });
        });
    });
</script>
