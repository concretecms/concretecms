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
<style>
@media (min-width: 992px) {
    #ccm-editor-preview.sticky {
        position: fixed;
    }
}
</style>
<form id="ccm-editor-config" method="post" class="ccm-dashboard-content-form" action="<?= $view->action('submit') ?>">
    <?php $token->output('submit') ?>
    <div class="row">
        <div class="col-md-6">
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
        </div>
        <div class="col-md-6">
            <div id="ccm-editor-preview" style="display: none">
                <?= $form->label('', t('Editor Preview')) ?>
                <div id="ccm-editor-preview-content"></div>
            </div>
        </div>
    </div>
            
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="pull-left btn btn-default" id="ccm-editor-preview-toggle"><?= t('Preview') ?></button>
            <button class="pull-right btn btn-primary" type="submit"><?= t('Save') ?></button>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
var $window = $(window),
    $togglePreview = $('#ccm-editor-preview-toggle'),
    $preview = $('#ccm-editor-preview'),
    $previewContainer = $preview.closest('div:not(#ccm-editor-preview)');
    $previewContent = $('#ccm-editor-preview-content'),
    previewEnabled = false;

function showPreview(show) {
    show = !!show;
    if (previewEnabled === show) {
        return;
    }
    previewEnabled = show;
    $togglePreview
        .removeClass(previewEnabled ? 'btn-default' : 'btn-success')
        .addClass(previewEnabled ? 'btn-success' : 'btn-default')
    ;
    if (previewEnabled) {
        updatePreview();
    } else {
        $preview.hide();
    }
}

function updatePreview() {
    if (!previewEnabled) {
        return;
    }
    var data = {
        <?= json_encode($token::DEFAULT_TOKEN_NAME) ?>: <?= json_encode($token->generate('ccm-editor-preview'))?>,
        previewHtml: $('textarea[name="preview"]').val() || ''
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
        $preview.show();
        $previewContent.html(data);
    })
}

var previewOffsetTop = $previewContainer.offset().top, previewOffsetLimit = $('.ccm-dashboard-page-header').offset().top;
function updatePreviewView() {
    if ($window.scrollTop() >= previewOffsetTop - previewOffsetLimit) {
        $preview
            .addClass('sticky')
            .css('top', previewOffsetTop - $previewContainer.offset().top + previewOffsetLimit + 'px');
    } else {
        $preview.removeClass('sticky');
    }
    if ($preview.css('position') === 'fixed') {
        $preview.width($previewContainer.width());
    } else {
        $preview.width('auto');
    }
}
updatePreviewView();
$window.on('scroll resize', function() {
    updatePreviewView();
});
    
$togglePreview.on('click', function(e) {
    e.preventDefault();
    showPreview(!previewEnabled);
});
$('#ccm-editor-config input').on('change', function() {
    updatePreview();
});

});
</script>
