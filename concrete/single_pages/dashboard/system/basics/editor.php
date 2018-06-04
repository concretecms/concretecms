<?php
/* @var Concrete\Core\Page\View\PageView $view */
/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Core\Form\Service\Form $form */

/* @var bool $filemanager */
/* @var bool $sitemap */

/* @var Concrete\Core\Editor\PluginManager $manager */
/* @var Concrete\Core\Editor\Plugin[] $plugins */
/* @var string[] $selected_hidden */

?>
<form id="ccm-editor-config" method="post" class="ccm-dashboard-content-form" action="<?= $view->action('submit') ?>">
    <?php $token->output('submit') ?>
    <?= $form->label('', t('concrete5 Extensions')) ?>
    <div class="checkbox">
        <label>
            <?= $form->checkbox('enable_filemanager', 1, $filemanager) ?>
            <?= t('Enable file selection from file manager.') ?>
        </label>
    </div>
    <div class="checkbox">
        <label>
            <?= $form->checkbox('enable_sitemap', 1, $sitemap) ?>
            <?= t('Enable page selection from sitemap.') ?>
        </label>
    </div>
    <?= $form->label('', t('Editor Plugins')) ?>
    <?php
    foreach ($plugins as $key => $plugin) {
        if (!in_array($key, $selected_hidden)) {
            $description = $plugin->getDescription();
            ?>
            <div class="checkbox">
                <label>
                    <?php
                    echo $form->checkbox('plugin[]', $key, $manager->isSelected($key));
                    if ($description !== '') {
                        echo '<span class="launch-tooltip" title="', h($description), '">';
                    }
                    echo h($plugin->getName());
                    if ($description !== '') {
                        echo '</span>';
                    }
                    ?>
                </label>
            </div>
            <?php
        }
    }
    ?>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="pull-left btn btn-default" id="ccm-editor-preview-toggle"><?= t('Preview') ?></button>
            <button class="pull-right btn btn-primary" type="submit"><?= t('Save') ?></button>
        </div>
    </div>
</form>

<div id="ccm-editor-preview-dialog" style="display: none" title="<?= t('Editor Preview') ?>">
    <div id="ccm-editor-preview-dialog-content"></div>
</div>
<script>
$(document).ready(function() {
var $togglePreview = $('#ccm-editor-preview-toggle'),
    $dialog = null,
    previewEnabled = false;

function showPreview(show) {
    show = !!show;
    if (previewEnabled === show) {
        return;
    }
    previewEnabled = show;
    $togglePreview.removeClass(previewEnabled ? 'btn-default' : 'btn-success').addClass(previewEnabled ? 'btn-success' : 'btn-default');
    if (previewEnabled) {
        updatePreview();
    } else {
        if ($dialog) {
            $dialog.dialog('close');
        }
    }
}

function updatePreview() {
    if (!previewEnabled) {
        return;
    }
    var data = {
        <?= json_encode($token::DEFAULT_TOKEN_NAME) ?>: <?= json_encode($token->generate('ccm-editor-preview'))?>
    };
    $('#ccm-editor-config input[type="checkbox"]:checked').each(function() {
        var $chk = $(this), name = $chk.attr('name');
        if (name.slice(-2) === '[]') {
            name = name.substr(0, name.length - 2);
            if (!(data[name] instanceof Array)) {
                data[name] = [];
            }
            data[name].push($chk.val());
        } else {
            data[name] = $chk.val();
        }
    });
    $.ajax({
        method: 'POST',
        url: <?= json_encode((string) URL::to('/ccm/system/dialogs/editor/settings/preview')) ?>,
        data: data
    })
    .success(function (data) {
        if (!$dialog) {
            $dialog = $('#ccm-editor-preview-dialog');
            $dialog.dialog({
                modal: false,
                resizable: true,
                height: 535,
                width: Math.max(200, $(window).width() / 2 - 100),
                close: function () {
                    showPreview(false);
                }
            })
        } else {
            $dialog.dialog('open');
        }
        $('#ccm-editor-preview-dialog-content').html(data);
    })
}
    
$togglePreview.on('click', function(e) {
    e.preventDefault();
    showPreview(!previewEnabled);
});
$('#ccm-editor-config input').on('change', function() {
    updatePreview();
});

});
</script>
