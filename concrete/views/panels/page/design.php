<?php
defined('C5_EXECUTE') or die("Access Denied.");
$selectedTemplateID = 0;
if (is_object($selectedTemplate)) {
    $selectedTemplateID = $selectedTemplate->getPageTemplateID();
}
$selectedThemeID = 0;
if (is_object($selectedTheme)) {
    $selectedThemeID = $selectedTheme->getThemeID();
    $customizer = $selectedTheme->getThemeCustomizer();
}

$selectedSkinIdentifier = '';
$skin = $c->getPageSkin();
if ($skin) {
    $selectedSkinIdentifier = $skin->getIdentifier();
}
?>
<section id="ccm-panel-page-design">
    <form method="post" action="<?= $controller->action('submit') ?>" data-panel-detail-form="design">
        <input type="hidden" name="update_theme" value="1" class="accept">
        <input type="hidden" name="processCollection" value="1">
        <input type="hidden" name="ptID" value="<?= $c->getPageTypeID() ?>" />

        <header>
            <a href="" data-panel-navigation="back" class="ccm-panel-back">
                <svg><use xlink:href="#icon-arrow-left" /></svg>
                <?= t('Page Settings') ?>
            </a>

            <h5><?= t('Design') ?></h5>
        </header>


        <div class="ccm-panel-content-inner" v-cloak data-view="preview-page-design">

        <?php
        if ($cp->canEditPageTemplate() && !$c->isGeneratedCollection()) {
            ?>
            <div class="ccm-panel-page-design-page-group" id="ccm-panel-page-design-page-templates"
                 data-panel-menu-id="page-templates">
                <div class="ccm-panel-page-design-title">
                    <?= t('Page Template') ?>
                </div>

                <div class="form-check" v-for="template in templates">
                    <input type="radio" class="form-check-input" name="pTemplateID" :id="template.pTemplateID" :value="template.pTemplateID" v-model="selectedTemplateID" />
                    <label class="form-check-label" :for="template.pTemplateID">
                        <span v-html="template.pTemplateIconImage"></span>
                        {{template.pTemplateDisplayName}}
                    </label>
                </div>

            </div>
            <?php
        }
        ?>

        <?php
        if ($cp->canEditPageTheme()) {
            ?>
            <div id="ccm-panel-page-design-themes" class="" data-panel-menu-id="themes">
                <div class="ccm-panel-page-design-title">
                    <?= t('Theme') ?>
                </div>

                <div class="mb-3">
                    <select class="form-select" id="selectTheme" name="pThemeID" v-model="selectedThemeID">
                        <option v-for="theme in themes" :value="theme.pThemeID">{{theme.pThemeDisplayName}}</option>
                    </select>
                </div>

                <div class="mb-3" v-if="skinsAvailable">
                    <label class="form-label" for="selectSkin"><?=t('Skin')?></label>
                    <select class="form-select" id="selectSkin" name="skinIdentifier" v-model="selectedSkinIdentifier" @change="reloadPreview">
                        <option v-for="skin in skins" :value="skin.identifier">{{skin.name}}</option>
                    </select>
                </div>
            </div>


            <?php
        }
        ?>

        </div>

        <div class="ccm-panel-content-inner">
            <div class="ccm-panel-page-design-page-group">
                <div class="ccm-panel-page-design-title">
                    <?=t('Summary Templates')?>
                </div>
                <p class="text-muted"><?=t2('One summary template available for this page.', '%s summary templates available for this page.',
                        $availableSummaryTemplatesCount)?></p>

                <div><small><a dialog-title="<?=t('Summary Templates')?>" class="dialog-launch" dialog-width="90%" dialog-height="70%"
                               href="<?=URL::to('/ccm/system/dialogs/page/summary_templates')?>?cID=<?=$c->getCollectionID()?>">
                            <?=t('Choose summary templates.')?>
                        </a></small></div>

            </div>

            <div class="ccm-panel-page-design-page-group">
            <?php
            if ($customizer && $customizer->supportsPageCustomization()) {
                $panelCustomizeTheme = URL::to('/ccm/system/panels/theme/customize/theme', $selectedThemeID, $c->getCollectionID());
                ?>
                <a class="btn btn-secondary btn-block mb-3" href="#" data-launch-sub-panel-url="<?=$panelCustomizeTheme?>">
                    <?=t('Customize')?>
                </a>
            <?php } ?>
            <?php
            if (Config::get('concrete.marketplace.enabled')) {
                ?>
                <div class="ccm-marketplace-btn-wrapper d-grid">
                    <button type="button" onclick="window.location.href='<?= URL::to('/dashboard/extend/themes') ?>'" class="btn-info btn btn-large"><?= t("Get More Themes") ?></button>
                </div>
                <?php
            }
            ?>
            </div>
        </div>
    </form>

</section>

<script type="text/javascript">

    $(function () {

        Concrete.Vue.activateContext('cms', function (Vue, config) {
            new Vue({
                el: 'div[data-view=preview-page-design]',
                components: config.components,
                computed: {
                    skinsAvailable: function() {
                        var my = this
                        var hasSkins = false
                        my.themes.forEach(function(theme) {
                            if (theme.pThemeID == my.selectedThemeID) {
                                hasSkins = theme.hasSkins;
                            }
                        })
                        return hasSkins
                    },
                    skins: function() {
                        var my = this
                        var skins = []
                        my.themes.forEach(function(theme) {
                            if (theme.pThemeID == my.selectedThemeID) {
                                skins = theme.skins
                            }
                        })
                        return skins
                    }
                },
                watch: {
                    selectedThemeID: function(value) {
                        if (this.skins && this.skins[0]) {
                            this.selectedSkinIdentifier = this.skins[0].identifier
                        } else {
                            this.selectedSkinIdentifier = ''
                        }
                        this.reloadPreview()
                    },
                    selectedTemplateID: function(value) {
                        this.reloadPreview()
                    }
                },
                methods: {
                    reloadPreview() {
                        my = this
                        var src = '<?= $controller->action(
                            "preview_contents"
                        ) ?>&pThemeID=' + my.selectedThemeID + '&skinIdentifier=' + my.selectedSkinIdentifier + '&pTemplateID=' + my.selectedTemplateID;
                        $('iframe[name=ccm-page-preview-frame]').get(0).src = src;
                    }
                },
                data: {
                    'selectedSkinIdentifier': '<?=$selectedSkinIdentifier?>',
                    'templates': <?=json_encode($templates)?>,
                    'selectedThemeID': <?=$selectedThemeID?>,
                    'selectedTemplateID': '<?=$selectedTemplateID?>',
                    'themes': <?=json_encode($themes)?>,
                }
            })
        })

    })

</script>
