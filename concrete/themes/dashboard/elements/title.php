<?php
defined('C5_EXECUTE') or die('Access Denied.');
/* @var \Concrete\Core\Filesystem\Element $_breadcrumb */
?>

<header>

    <div class="ccm-dashboard-header-row">
        <div class="ccm-dashboard-breadcrumb">
            <?php
            if (isset($_breadcrumb) && $_breadcrumb instanceof \Concrete\Core\Filesystem\Element) {
                $_breadcrumb->render();
            }
            ?>
        </div>

        <div class="ccm-dashboard-header-search">
            <?php
            if (isset($headerSearch) && $headerSearch instanceof \Concrete\Core\Filesystem\Element) {
                $headerSearch->render();
            }
            ?>
        </div>

    </div>

    <div class="ccm-dashboard-header-row">

        <div class="ccm-dashboard-header-title">
            <?php if (isset($_bookmarked)) { ?>
                <a href="#" class="ccm-dashboard-page-header-bookmark" data-page-id="<?= $c->getCollectionID(); ?>"
                   data-token="<?= $token->generate('access_bookmarks'); ?>"
                   data-bookmark-action="<?= $_bookmarked ? 'remove-favorite' : 'add-favorite'; ?>">
                <span class="header-icon">
                    <svg class="icon-bookmark <?= $_bookmarked ? 'bookmarked' : ''; ?>">
                        <use xlink:href="#icon-bookmark-page"/>
                    </svg>
                </span>
                </a>
            <?php } ?>

            <h1><?= (isset($pageTitle) && $pageTitle) ? h(t($pageTitle)) : '&nbsp;'; ?></h1>
        </div>

        <div class="ccm-dashboard-header-menu">

            <?php if (isset($headerMenu) && ($headerMenu instanceof \Concrete\Core\Controller\ElementController ||
                $headerMenu instanceof \Concrete\Core\Filesystem\Element)) { ?>
                <?= $headerMenu->render(); ?>
            <?php } ?>

        </div>

    </div>


</header>
