<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-ui" data-header="page-search">
    <?php if (isset($includeBreadcrumb) && $includeBreadcrumb) { ?>
        <div class="ccm-search-results-breadcrumb">
        </div>
    <?php } ?>

    <form class="form-inline" method="get" action="<?php echo URL::to('/ccm/system/search/pages/basic') ?>">

        <div class="ccm-header-search-form-input">
            <a class="ccm-header-reset-search" href="#" data-button-action-url="<?php echo URL::to('/ccm/system/search/pages/clear')?>" data-button-action="clear-search"><?php echo t('Reset Search')?></a>
            <a class="ccm-header-launch-advanced-search" href="<?php echo URL::to('/ccm/system/dialogs/page/advanced_search')?>" data-launch-dialog="advanced-search"><?php echo t('Advanced')?></a>
            <input type="text" class="form-control" autocomplete="off" name="cKeywords" placeholder="<?php echo t('Search')?>">
        </div>

        <?php
        $site = Core::make('site')->getActiveSiteForEditing();
        $locales = $site->getLocales();
        if (count($locales) > 1) {
            $selectedLocale = $site->getDefaultLocale();
            ?>

            <input type="hidden" name="siteTreeID" value="<?=$selectedLocale->getSiteTree()->getSiteTreeID()?>">

            <div class="btn-group">
                <button type="button" class="btn btn-default" data-action="launch-locale-list" data-toggle="dropdown">
                    <?= \Concrete\Core\Multilingual\Service\UserInterface\Flag::getLocaleFlagIcon($selectedLocale) ?> <?= $selectedLocale->getLanguageText() ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <?php foreach ($locales as $locale) { ?>
                        <li><a href="#" <?php if ($selectedLocale->getLocaleID() == $locale->getLocaleID()) { ?>data-locale="default"<?php } ?> data-select-locale-tree="<?=$locale->getSiteTree()->getSiteTreeID()?>">
                                <?= \Concrete\Core\Multilingual\Service\UserInterface\Flag::getLocaleFlagIcon($locale) ?>
                                <?= $locale->getLanguageText() ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>

            <script type="text/javascript">
                $(function() {
                    $('a[data-select-locale-tree]').on('click', function(e) {
                        e.preventDefault();
                        var treeID = $(this).attr('data-select-locale-tree'),
                            html = $(this).html() + ' <span class="caret"></span>',
                            $form = $(this).closest('form');

                        $form.find('input[name=siteTreeID]').val(treeID);
                        $form.find('button[data-action=launch-locale-list]').html(html);
                    });
                });
            </script>
        <?php } ?>

        <button class="btn btn-info" type="submit"><i class="fa fa-search"></i></button>

    </form>

</div>

