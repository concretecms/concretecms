<?php
defined('C5_EXECUTE') or die("Access Denied.");
$pk = PermissionKey::getByHandle('customize_themes');
/* @var Concrete\Core\Page\Page $c */
/* @var Concrete\Controller\Panel\Page\Design\Customize $controller */
/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Core\StyleCustomizer\Preset[] $presets */
/* @var int $sccRecordID */
/* @var Concrete\Core\StyleCustomizer\Preset $selectedPreset */
/* @var Concrete\Core\StyleCustomizer\Set[] $styleSets */
/* @var Concrete\Theme\Elemental\PageTheme $theme */
/* @var Concrete\Core\View\DialogView $this */
/* @var Concrete\Core\User\User $u */
/* @var Concrete\Core\StyleCustomizer\Style\ValueList $valueList */
/* @var Concrete\Core\View\DialogView $view */
/* @var Concrete\Core\Validation\CSRF\Token $token */

?>
<section id="ccm-panel-page-design-customize">
    <form data-form="panel-page-design-customize" target="ccm-page-preview-frame" method="post" action="<?= $controller->action("preview", $theme->getThemeID()) ?>">
        <header>
            <a href="" data-panel-navigation="back" class="ccm-panel-back"><span class="fa fa-chevron-left"></span></a>
            <a href="" data-panel-navigation="back"><?= t('Customize Theme') ?></a>
        </header>
        <?php
        if (count($presets) > 1) {
            ?>
            <div class="ccm-panel-content-inner">
                <div class="list-group" data-panel-menu-id="page-design-presets"  data-panel-menu="collapsible-list-group">
                    <div class="list-group-item list-group-item-header"><?= t('Preset') ?></div>
                    <?php
                    $i = 0;
                    foreach ($presets as $preset) {
                        $selected = false;
                        if (is_object($selectedPreset) && $selectedPreset->getPresetHandle() == $preset->getPresetHandle()) {
                            $selected = true;
                        }
                        ?>
                        <label class="list-group-item clearfix">
                            <input type="radio" class="ccm-flat-radio" value="<?= $preset->getPresetHandle() ?>" name="handle"<?= $selected ? ' checked="checked"' : '' ?> />
                            <?= $preset->getPresetDisplayName() ?>
                            <?= $preset->getPresetIconHTML() ?>
                        </label>
                        <?php
                        if ($i == 0) {
                            ?>
                            <div class="list-group-item-collapse-wrapper">
                            <?php
                        }
                        ++$i;
                    }
                    ?>
                    </div>
                    <a class="list-group-item list-group-item-collapse" href="#"><span><?= t('Expand') ?></span></a>
                </div>
            </div>
            <?php
        }
        // output basic values –these are ones we don't have any
        // kind of special mapping for and that don't appear in our customizer style sets.
        foreach ($valueList->getValues() as $value) {
            if ($value instanceof \Concrete\Core\StyleCustomizer\Style\Value\BasicValue) {
                ?><input type="hidden" name="<?= $value->getVariable() ?>" value="<?= $value->getValue() ?>" /><?php
            }
        }
        ?>
        <div id="ccm-panel-page-design-customize-list">
            <?php
            foreach ($styleSets as $set) {
                ?>
                <div class="ccm-panel-page-design-customize-style-set">
                    <h5 class="ccm-panel-page-design-customize-style-set-collapse"><?= $set->getDisplayName() ?></h5>
                    <ul class="list-unstyled">
                        <?php
                        foreach ($set->getStyles() as $style) {
                            $value = $style->getValueFromList($valueList);
                            ?><li><?= $style->getDisplayName() ?> <?= $style->render($value) ?></li><?php
                        }
                        ?>
                    </ul>
                </div>
                <?php
            }
            ?>
            <div class="ccm-panel-page-design-customize-style-set">
                <h5 class="ccm-panel-page-design-customize-style-set-collapse"><?= t('Advanced') ?></h5>
                <ul class="list-unstyled">
                    <li>
                        <?= t('Custom CSS') ?>
                        <input type="hidden" name="sccRecordID" value="<?= $sccRecordID ?>" />
                    </li>
                </ul>
            </div>
        </div>
        <div style="padding:0 30px">
            <br />
            <div class="btn-group">
                <button id="ccm-style-customizer-copy" class="btn btn-sm btn-default launch-tooltip" title="<?= t('Copy') ?>" disabled="disabled"><i class="fa fa-copy"></i></button>
                <button id="ccm-style-customizer-paste" class="btn btn-sm btn-default launch-tooltip" title="<?= t('Paste') ?>" disabled="disabled"><i class="fa fa-clipboard"></i></button>
                <button id="ccm-style-customizer-export" class="btn btn-sm btn-default launch-tooltip" title="<?= t('Export') ?>"><i class="fa fa-sign-out"></i></button>
                <button id="ccm-style-customizer-import" class="btn btn-sm btn-default launch-tooltip" title="<?= t('Import') ?>"><i class="fa fa-sign-in"></i></button>
            </div>
            <div class="pull-right">
                <button class="btn btn-sm btn-danger launch-tooltip" data-panel-detail-action="reset" title="<?= t('Reset Customizations') ?>"><i class="fa fa-undo"></i></button>
            </div>
            <br />
            <br />
        </div>
    </form>
</section>

<div class="ccm-panel-detail-form-actions">
    <button class="pull-right btn btn-success" type="button" data-panel-detail-action="customize-design-submit"><?= t('Save Changes') ?></button>
</div>

<div class="hide">
    <div id="ccm-style-customizer-export-dialog" class="ccm-ui" title="<?= t('Export') ?>">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <textarea readonly="readonly" class="form-control" style="height: 430px; resize: none; cursor: text; font-family: Menlo, Monaco, Consolas, 'Courier New', monospace" onclick="this.select()"></textarea>
                </div>
            </div>
        </div>
        <div class="dialog-buttons">
            <button class="btn btn-primary pull-right" onclick="$.fn.dialog.closeTop()"><?= t('Close') ?></button>
        </div>
    </div>
    <div id="ccm-style-customizer-import-dialog" class="ccm-ui" title="<?= t('Import') ?>">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <textarea class="form-control" style="height: 430px; resize: none; font-family: Menlo, Monaco, Consolas, 'Courier New', monospace"></textarea>
                </div>
            </div>
        </div>
        <div class="dialog-buttons">
            <button class="btn btn-default pull-left" onclick="$.fn.dialog.closeTop()"><?= t('Cancel') ?></button>
            <button class="btn btn-primary pull-right" id="ccm-style-customizer-import-dialog-go"><?= t('Import') ?></button>
        </div>
    </div>
</div>

<script>
ConcretePageDesignPanel = {

    applyDesignToPage: function() {
        var $form = $('form[data-form=panel-page-design-customize]'),
            panel = ConcretePanelManager.getByIdentifier('page');

        $form.prop('target', null);
        $form.attr('action', <?= json_encode((string) $controller->action("apply_to_page", $theme->getThemeID())) ?>);
        $form.concreteAjaxForm();
        $form.submit();
    },

    applyDesignToSite: function() {
        var $form = $('form[data-form=panel-page-design-customize]'),
            panel = ConcretePanelManager.getByIdentifier('page');

        $form.prop('target', null);
        $form.attr('action', <?= json_encode((string) $controller->action("apply_to_site", $theme->getThemeID())) ?>);
        $form.concreteAjaxForm();
        $form.submit();
    },

    resetPageDesign: function() {
        var $form = $('form[data-form=panel-page-design-customize]'),
            panel = ConcretePanelManager.getByIdentifier('page');

        $form.prop('target', null);
        $form.attr('action', <?= json_encode((string) $controller->action("reset_page_customizations")) ?>);
        $form.concreteAjaxForm();
        $form.submit();
    },

    resetSiteDesign: function() {
        var $form = $('form[data-form=panel-page-design-customize]'),
            panel = ConcretePanelManager.getByIdentifier('page');

        $form.prop('target', null);
        $form.attr('action', <?= json_encode((string) $controller->action("reset_site_customizations", $theme->getThemeID())) ?>);
        $form.concreteAjaxForm();
        $form.submit();
    }

};

$(function() {
    panel = ConcretePanelManager.getByIdentifier('page');
    $('button[data-panel-detail-action=customize-design-submit]').on('click', function() {
        <?php
        if ($pk->validate()) {
            ?>
            panel.showPanelConfirmationMessage(
                'page-design-customize-apply',
                <?= json_encode(t('Apply this design to just this page, or your entire site?')) ?>,
                [
                    {'class': 'btn btn-primary pull-right', 'onclick': 'ConcretePageDesignPanel.applyDesignToSite()', 'style': 'margin-left: 10px', 'text': <?= json_encode(t("Entire Site")) ?>},
                    {'class': 'btn btn-default pull-right', 'onclick': 'ConcretePageDesignPanel.applyDesignToPage()', 'text': <?= json_encode(t("This Page")) ?>}
                ]
            );
            <?php 
        } else {
            ?>
            ConcretePageDesignPanel.applyDesignToPage();
            <?php 
        }
        ?>
        return false;
    });
    $('div[data-panel-menu-id=page-design-presets]').on('change', $('input[type=radio]'), function() {
        var selectedpreset = $('div[data-panel-menu-id=page-design-presets] input[type=radio]:checked');
        if (selectedpreset.length) {
            var panel = ConcretePanelManager.getByIdentifier('page');
            var $panel = $('#' + panel.getDOMID());
            var url = <?= json_encode((string) URL::to('/ccm/system/panels/page/design/customize', $theme->getThemeID()) . '?cID=' . $c->getCollectionID()) ?>;
            var content = $(this).closest('div.ccm-panel-content');
            $.concreteAjax({
                url: url,
                dataType: 'html',
                data: {'handle': $(this).find(':checked').val()},
                success: function(r) {
                    content.html(r);
                    panel.onPanelLoad(this);
                    $('form[data-form=panel-page-design-customize]').submit();
                }
            });
        }
    });
    $('div.ccm-panel-page-design-customize-style-set').on('click', 'h5', function() {
        var $list = $(this).parent().find('> ul');
        var height = $list.height();
        var $header = $(this);
        if ($(this).hasClass('ccm-panel-page-design-customize-style-set-expand')) {
            $list.queue(function() {
                $(this).css('height', 0);
                $(this).show();
                $(this).dequeue();
            }).
            delay(5).queue(function() {
                $(this).css('height', height);
                $header.removeClass('ccm-panel-page-design-customize-style-set-expand').addClass('ccm-panel-page-design-customize-style-set-collapse');
                $(this).dequeue();
            });
        } else {
            $list.css('height', height);
            $list.queue(function() {
                $(this).css('height', height);
                $(this).dequeue();
            }).
            delay(0).queue(function() {
                $(this).css('height', 0);
                $header.removeClass('ccm-panel-page-design-customize-style-set-collapse').addClass('ccm-panel-page-design-customize-style-set-expand');
                $(this).dequeue();
            }).
            delay(305).queue(function() {
                $(this).hide();
                $(this).css('height', 'auto');
                $(this).dequeue();
            });
        }
    });

    $('form[data-form=panel-page-design-customize] input[name="sccRecordID"]').concreteStyleCustomizerCustomCss({
        cID: <?= (int) $c->getCollectionID() ?>,
        edit: {
            tokenName: <?= json_encode($token::DEFAULT_TOKEN_NAME) ?>,
            tokenValue: <?= json_encode($token->generate('ccm-style-customizer-customcss-edit')) ?>,
            url: <?= json_encode((string)  URL::to('/ccm/system/dialogs/page/design/css')) ?>
        },
        loadCss: {
            tokenName: <?= json_encode($token::DEFAULT_TOKEN_NAME) ?>,
            tokenValue: <?= json_encode($token->generate('ccm-style-customizer-customcss-load')) ?>,
            url: <?= json_encode((string)  URL::to('/ccm/system/dialogs/page/design/css/get')) ?>
        },
        saveCss: {
            tokenName: <?= json_encode($token::DEFAULT_TOKEN_NAME) ?>,
            tokenValue: <?= json_encode($token->generate('ccm-style-customizer-customcss-save')) ?>,
            url: <?= json_encode((string)  URL::to('/ccm/system/dialogs/page/design/css/set')) ?>
        },
        i18n: {
            editTitle: <?= json_encode(t('Custom CSS')) ?>
        }
    });

    $('button[data-panel-detail-action=reset]').unbind().on('click', function() {
        <?php
        if ($pk->validate()) {
            ?>
            panel.showPanelConfirmationMessage('page-design-customize-apply', <?= json_encode(t('Reset the theme customizations for just this page, or your entire site?')) ?>, [
                {'class': 'btn btn-sm btn-primary pull-right', 'onclick': 'ConcretePageDesignPanel.resetSiteDesign()', 'style': 'margin-left: 10px', 'text': <?= json_encode(t("Entire Site")) ?>},
                {'class': 'btn btn-sm btn-default pull-right', 'onclick': 'ConcretePageDesignPanel.resetPageDesign()', 'text': <?= json_encode(t("This Page")) ?>}
            ]);
            <?php 
        } else {
            ?>
            ConcretePageDesignPanel.resetPageDesign();
            <?php 
        }
        ?>
        return false;
    });

    function exportStyles(onReady) {
        var $styles = $('#ccm-panel-page-design-customize-list .ccm-style-customizer-importexport'),
            data = {},
            exportNext = function (index, done) {
                if (index >= $styles.length) {
                    done();
                    return;
                }
                var exporter = $($styles[index]).data('ccm-style-customizer-importexport');
                exporter.exportStyle(data, function(error) {
                    if (error) {
                        done(error);
                        return;
                    }
                    exportNext(index + 1, done);
                });
            };
        exportNext(0, function(error) {
            if (error) {
                ConcreteAlert.dialog(ccmi18n.error, error);
            } else {
                onReady(data);
            }
        });
    }

    function importStyles(data, onCompleted) {
        var $styles = $('#ccm-panel-page-design-customize-list .ccm-style-customizer-importexport'),
            errors = [],
            importNext = function (index, done) {
                if (index >= $styles.length) {
                    done();
                    return;
                }
                var importer = $($styles[index]).data('ccm-style-customizer-importexport');
                importer.importStyle(data, function(error) {
                    if (error) {
                        errors.add(error);
                        return;
                    }
                    importNext(index + 1, done);
                });
            };
        importNext(0, function () {
            ConcreteEvent.publish('StyleCustomizerControlUpdate');
            if (errors.length > 0) {
                ConcreteAlert.dialog(ccmi18n.error, errors.join('\n'));
            }
            if (onCompleted) {
                onCompleted(errors);
            }
        });
    }

    $('#ccm-style-customizer-export').on('click', function (e) {
        e.preventDefault();
        exportStyles(function(data) {
            var $dlg = $('#ccm-style-customizer-export-dialog'),
                $textarea = $dlg.find('textarea');
            $textarea.val(JSON.stringify(data, null, '    '));
            $.fn.dialog.open({
                width: 800,
                height: 450,
                element: $dlg,
                onOpen: function() {
                    $textarea.focus();
                    $textarea.select();
                }
            });
        });
    });

    $('#ccm-style-customizer-import').on('click', function (e) {
        e.preventDefault();
        var $dlg = $('#ccm-style-customizer-import-dialog'),
            $textarea = $dlg.find('textarea');
        $textarea.val('');
        $.fn.dialog.open({
            width: 800,
            height: 450,
            element: $dlg,
            onOpen: function() {
                $textarea.focus();
            }
        });
        $('#ccm-style-customizer-import-dialog-go').off('click').on('click', function() {
            var json = $.trim($textarea.val());
            if (json === '') {
                $textarea.focus();
                return;
            }
            var data;
            try {
                data = JSON.parse(json);
            } catch (e) {
                data = null;
            }
            if (!$.isPlainObject(data)) {
                ConcreteAlert.dialog(
                   ccmi18n.error,
                   <?= json_encode(t('Invalid data.')) ?>,
                   function() {
                       $textarea.focus();
                   }
                );
                return;
            }
            importStyles(
                data,
                function(errors) {
                    if (errors.length === 0) {
                        $dlg.dialog('close');
                        ConcreteAlert.notify({
                            message: <?= json_encode(t('The style settings have been applied.')) ?>
                        });
                    }
                }
            );
        });
    });

    if (window.localStorage && window.localStorage.getItem && window.localStorage.setItem) {
        $('#ccm-style-customizer-copy')
            .on('click', function (e) {
                e.preventDefault();
                exportStyles(function(data) {
                    window.localStorage.setItem('ccm-style-customizer-data', JSON.stringify(data));
                    checkLSPaste();
                    ConcreteAlert.notify({
                        message: <?= json_encode(t('The style settings have been copied.')) ?>
                    });
                });
            })
            .removeAttr('disabled')
        ;
        $('#ccm-style-customizer-paste').on('click', function (e) {
            e.preventDefault();
            var data = getLSData();
            if (!$.isPlainObject(data)) {
                ConcreteAlert.dialog(ccmi18n.error, <?= json_encode(t('No custom CSS to be pasted.')) ?>);
                return;
            }
            importStyles(
                data,
                function (errors) {
                    if (errors.length === 0) {
                        ConcreteAlert.notify({
                            message: <?= json_encode(t('The style settings have been applied.')) ?>
                        });
                    }
                }
            );
        });
        function getLSData() {
            var json = window.localStorage.getItem('ccm-style-customizer-data');
            try {
                return json ? JSON.parse(json) : null;
            } catch (e) {
                return null;
            }
        }
        function checkLSPaste() {
            if (!getLSData()) {
                $('#ccm-style-customizer-paste').attr('disabled', 'disabled');
                return;
            }
            $('#ccm-style-customizer-paste').removeAttr('disabled');
        }
        $(window).on('storage', function() {
            checkLSPaste();
        });
        checkLSPaste();
    }

    ConcreteEvent.unsubscribe('StyleCustomizerControlUpdate');
    ConcreteEvent.subscribe('StyleCustomizerControlUpdate', function() {
        $('form[data-form=panel-page-design-customize]').submit();
        $('div[data-panel-menu-id=page-design-presets] input[type=radio]').prop('checked', false);
    });
});
</script>
