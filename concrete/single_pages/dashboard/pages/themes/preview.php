<?php

defined('C5_EXECUTE') or die("Access Denied.");

$ui = new \Concrete\Core\Application\Service\UserInterface();
$config = Core::make('config');
$show_titles = (bool) $config->get('concrete.accessibility.toolbar_titles');
$show_tooltips = (bool) $config->get('concrete.accessibility.toolbar_tooltips');
$large_font = (bool) $config->get('concrete.accessibility.toolbar_large_font');
$panelCustomizeTheme = URL::to('/ccm/system/panels/theme/customize/theme', $customizeTheme->getThemeID(), $previewPage->getCollectionID());
$previewContentsURL = URL::to('/ccm/system/panels/page/design/preview_contents') . '?cID=' . $previewPage->getCollectionID();
?>

<?= View::element('icons') ?>
<div id="ccm-page-controls-wrapper" class="ccm-ui">
    <div id="ccm-toolbar">
        <ul class="ccm-toolbar-item-list">
            <li class="ccm-logo float-start"><span><?= $ui->getToolbarLogoSRC() ?></span></li>
            <li class="float-start">
                <a href="<?= URL::to('/dashboard/pages/themes', 'view') ?>">
                    <svg>
                        <use xlink:href="#icon-arrow-left"/>
                    </svg>
                </a>
            </li>
            <li class="float-start d-none d-md-block">
                <a href="#" <?php
                   if ($show_tooltips) { ?>class="launch-tooltip"<?php } ?>
                   data-toggle="tooltip"
                   data-placement="bottom"
                   data-delay='{ "show": 500, "hide": 0 }'
                   data-launch-panel="customize-theme"
                   title="<?= t('Skins and Customizer') ?>">
                    <svg>
                        <use xlink:href="#icon-pencil"/>
                    </svg>
                    <span class="ccm-toolbar-accessibility-title"><?= tc(
                            'toolbar',
                            'Preview &amp; Customize'
                        ) ?></span>
                </a>
            </li>
            <?php if (isset($documentationNavigation)) {

                $documentationPages = $documentationNavigation->getItems();

                ?>
                <li class="ccm-toolbar-button-with-text float-end me-4">
                    <a class="dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false"><?=$customizeTheme->getThemeName()?></a>
                    <ul class="dropdown-menu">
                        <?php foreach($documentationPages as $item) { ?>
                            <?php if (count($item->getChildren())) { ?>
                                <li><h6 class="dropdown-header"><?=$item->getName()?></h6></li>
                                <?php foreach($item->getChildren() as $child) { ?>
                                    <li><a class="dropdown-item" href="<?=$child->getURL()?>"><?=t($child->getName())?></a></li>
                                <?php } ?>
                            <?php } else { ?>
                                <li><a class="dropdown-item" href="<?=$item->getURL()?>"><?=t($item->getName())?></a></li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>

<div data-vue="theme-customizer" class="h-100">

    <div class="ccm-page h-100">
    <iframe name="ccm-page-preview-frame" class="ccm-page-preview-frame" style="margin-top: 48px" class="w-100 h-100" style="border: 0"
            src="<?=$previewContentsURL?>"></iframe>
    </div>

</div>
<script type="text/javascript">

    $(function () {
        $('html').addClass('h-100 ccm-panel-ready')
        $('body, body > .ccm-ui').addClass('h-100');
        ConcretePanelManager.register({'identifier': 'customize-theme', 'position': 'left', url: '<?=$panelCustomizeTheme?>'});
        ConcreteToolbar.start();
    });

</script>