<?php

defined('C5_EXECUTE') or die("Access Denied.");

$ui = new \Concrete\Core\Application\Service\UserInterface();

?>

<?=View::element('icons')?>
<div id="ccm-page-controls-wrapper" class="ccm-ui">
    <div id="ccm-toolbar">
        <ul>
            <li class="ccm-logo float-left"><span><?= $ui->getToolbarLogoSRC() ?></span></li>
            <li class="float-left">
                <a href="<?=URL::to('/dashboard/pages/themes', 'view')?>">
                    <i class="fa fa-arrow-left"></i></span>
                </a>
            </li>
        </ul>
    </div>
</div>

<div v-cloak data-vue="theme-customizer">

    <div id="ccm-panel-dashboard" class="d-none d-md-block ccm-panel ccm-panel-left ccm-panel-active ccm-panel-loaded">
        <div class="ccm-panel-content-wrapper ccm-ui">
            <div class="ccm-panel-content ccm-panel-content-visible">

                <h4><?=t('Skins')?></h4>
                <div class="card mb-4">
                    <ul class="list-group list-group-flush">
                        <li v-for="skin in skins" class="list-group-item">
                            <label class="mb-0">
                            <input name="selectedSkin" type="radio" v-model="selectedSkin" :value="skin">
                            {{skin.name}}
                            </label>
                        </li>
                    </ul>
                </div>

                <div>
                    <div v-for="set in styles.sets">
                        <h5>{{set.name}}</h5>
                        <div class="card mb-4">
                            <ul class="list-group list-group-flush">
                                <li v-for="style in set.styles" class="list-group-item">
                                    {{style.name}}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <div id="ccm-panel-detail-page-design" class="ccm-panel-detail ccm-panel-detail-transition-fade ccm-panel-detail-transition-fade-apply">
        <div class="ccm-ui ccm-panel-detail-content">
            <iframe id="ccm-page-preview-frame" name="ccm-page-preview-frame" src="http://concrete5.test/index.php/ccm/system/panels/page/design/preview_contents?ccm_token=1614603462:2e8f040bb871f0935fcea7b38962d44c&amp;cID=219&amp;pThemeID=1&amp;pTemplateID=2"></iframe>
            <div class="ccm-panel-detail-form-actions">
                <button class="float-right btn btn-success" type="button" data-panel-detail-action="submit">Save Changes</button>
            </div>
        </div>
    </div>

</div>
<script type="text/javascript">

    $(function() {
        Concrete.Vue.activateContext('cms', function (Vue, config) {
            new Vue({
                el: 'div[data-vue=theme-customizer]',
                components: config.components,
                data: {
                    skins: <?=json_encode($skins)?>,
                    selectedSkin: <?=json_encode($selectedSkin)?>,
                    styles: <?=json_encode($styles)?>,
                },
                mounted() {
                    // Kind of a hack to force the panels to push content properly.
                    $('html').addClass('ccm-panel-open ccm-panel-left')
                }
            })
        })
    });

</script>