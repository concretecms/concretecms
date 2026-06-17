<?php

use Concrete\Core\Support\Facade\Url as UrlFacade;

defined('C5_EXECUTE') or die('Access Denied.');

/** @var \Concrete\Core\Page\Type\Type[] $frequentPageTypes */
$frequentPageTypes = $frequentPageTypes ?? [];
/** @var \Concrete\Core\Page\Type\Type[] $otherPageTypes */
$otherPageTypes = $otherPageTypes ?? [];
/** @var \Concrete\Core\Page\Page[] $drafts */
$drafts = $drafts ?? [];
$canViewSitemap = $canViewSitemap ?? false;
?>

<?php if (count($frequentPageTypes) || count($otherPageTypes)) { ?>
    <header><h5><?= t('New Page') ?></h5></header>
    <section>
        <menu>
            <?php foreach ($frequentPageTypes as $pt) { ?>
                <li>
                    <a href="<?= UrlFacade::to('/ccm/system/page/', 'create', $pt->getPageTypeID()) ?>">
                        <?= $pt->getPageTypeDisplayName() ?>
                    </a>
                </li>
            <?php } ?>

            <?php foreach ($otherPageTypes as $pt) { ?>
                <li data-page-type="other" style="<?= count($frequentPageTypes) ? 'display: none' : '' ?>">
                    <a href="<?= UrlFacade::to('/ccm/system/page/', 'create', $pt->getPageTypeID()) ?>">
                        <?= $pt->getPageTypeDisplayName() ?>
                    </a>
                </li>
            <?php } ?>

            <?php if (count($frequentPageTypes) && count($otherPageTypes)) { ?>
                <li class="ccm-panel-sitemap-more-page-types">
                    <a href="#" data-sitemap="show-more">
                        <i class="fas fa-caret-down"></i> <?= t('More') ?>
                    </a>
                </li>
            <?php } ?>
        </menu>
    </section>
    <script type="text/javascript">
        $(function () {
            $('a[data-sitemap=show-more]').on('click', function (e) {
                e.preventDefault();
                $('li[data-page-type=other]').show();
                $(this).parent().remove();
            });
        });
    </script>
    <?php
} ?>

<?php if (count($drafts)) { ?>
    <header><h5><?= t('Page Drafts') ?></h5></header>
    <menu>
        <?php foreach ($drafts as $dc) { ?>
            <li>
                <a href="<?= $dc->getCollectionLink() ?>">
                    <?php if ($dc->getCollectionName()) {
                        echo $dc->getCollectionName() . ' ' . app('date')->formatDateTime(
                                        $dc->getCollectionDateAdded(),
                                        false
                                );
                    } else {
                        echo t('(Untitled)') . ' ' . app('date')->formatDateTime(
                                        $dc->getCollectionDateAdded(),
                                        false
                                );
                    }
                    ?>
                </a>
            </li>
        <?php } ?>
    </menu>
<?php } ?>

<?php if ($canViewSitemap) { ?>
    <header><h5><?= t('Sitemap') ?></h5></header>
    <hr>
    <div id="ccm-sitemap-panel-sitemap"></div>
    <script type="text/javascript">
        $(function () {
            $('#ccm-sitemap-panel-sitemap').concreteSitemap({
                onClickNode: function (node) {
                    window.location.href = node.data.link;
                }
            });
        });
    </script>
<?php } ?>

