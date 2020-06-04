<?php

use Concrete\Core\Application\UserInterface\Dashboard\Navigation\FavoritesNavigationFactory;
use Concrete\Core\Application\UserInterface\Dashboard\Navigation\NavigationFactory;
use Concrete\Core\User\User;

defined('C5_EXECUTE') or die("Access Denied.");

$menuFactory = app(NavigationFactory::class);
$favoritesFactory = app(FavoritesNavigationFactory::class);
$u = app(User::class);

$topLevelMenu = $menuFactory->createTopLevelNavigation();
$favorites = $favoritesFactory->createNavigation();
$sectionMenu = $menuFactory->createSectionNavigation($c);
$ui = UserInfo::getByID($u->getUserID());
?>
<section data-view="dashboard-panel" v-cloak>

    <!--
    <menu class="ccm-panel-dashboard-favorites-menu">
        <li :class="{active: activeTab === 'dashboard'}"><a @click="activeTab = 'dashboard'" href="#"
                                                            id="panel-dashboard-dashboard"
                                                            data-toggle="pill"><?= t('Dashboard') ?></a></li>
        <li :class="{active: activeTab === 'favorites'}"><a @click="activeTab = 'favorites'" href="#"
                                                            id="panel-dashboard-favorites"
                                                            data-toggle="pill"><?= t('Favorites') ?></a></li>
    </menu>

    <ul class="nav flex-column" v-show="activeTab === 'dashboard'">
        <li v-for="item in topLevelMenu"><a :href="item.url">{{item.name}}</a></li>
    </ul>
//-->

    <header>
        <a href="" class="ccm-panel-back">
            <svg><use xlink:href="#icon-arrow-left" /></svg>
            <?= t('Dashboard') ?>
        </a>

        <h5><?= t('System and Settings') ?></h5>
    </header>

    <ul class="nav flex-column">
        <li class="">
            <a href="http://andrewembler.com/index.php/dashboard/system/basics/">Basics</a>
        </li>

        <li class="">
            <a href="http://andrewembler.com/index.php/dashboard/system/multilingual/">Multilingual</a>
        </li>

        <li class="">
            <a href="http://andrewembler.com/index.php/dashboard/system/seo/">SEO &amp; Statistics</a>
        </li>

        <li class="nav-path-selected">
            <a href="http://andrewembler.com/index.php/dashboard/system/files/">Files</a>
            <ul class="nav flex-column">
                <li class="">
                    <a href="http://andrewembler.com/index.php/dashboard/system/files/permissions/">File Manager Permissions</a>
                </li>

                <li class="nav-selected nav-path-selected">
                    <a href="http://andrewembler.com/index.php/dashboard/system/files/filetypes/">Allowed File Types</a>
                </li>

                <li class="">
                    <a href="http://andrewembler.com/index.php/dashboard/system/files/thumbnails/">Thumbnails</a>
                </li>

                <li class="">
                    <a href="http://andrewembler.com/index.php/dashboard/system/files/storage/">File Storage Locations</a>
                </li>

                <li class="">
                    <a href="http://andrewembler.com/index.php/dashboard/system/files/image_uploading/">Image Options</a>
                </li>

                <li class="">
                    <a href="http://andrewembler.com/index.php/dashboard/system/files/export_options/">Export Options</a>
                </li>

            </ul>                        </li>

        <li class="">
            <a href="http://andrewembler.com/index.php/dashboard/system/optimization/">Optimization</a>
        </li>

        <li class="">
            <a href="http://andrewembler.com/index.php/dashboard/system/permissions/">Permissions &amp; Access</a>
        </li>

        <li class="">
            <a href="http://andrewembler.com/index.php/dashboard/system/registration/">Login &amp; Registration</a>
        </li>

        <li class="">
            <a href="http://andrewembler.com/index.php/dashboard/system/mail/">Email</a>
        </li>

        <li class="">
            <a href="http://andrewembler.com/index.php/dashboard/system/conversations/">Conversations</a>
        </li>

        <li class="">
            <a href="http://andrewembler.com/index.php/dashboard/system/attributes/">Attributes</a>
        </li>

        <li class="">
            <a href="http://andrewembler.com/index.php/dashboard/system/environment/">Environment</a>
        </li>

        <li class="">
            <a href="http://andrewembler.com/index.php/dashboard/system/express/">Express</a>
        </li>

        <li class="nav-divider package-page-divider"></li>                        <li class="">
            <a href="http://andrewembler.com/index.php/dashboard/system/google_analytics/">Google Analytics</a>
        </li>

        <li class="">
            <a href="http://andrewembler.com/index.php/dashboard/system/update/">Update concrete5</a>
        </li>

        <li class="nav-divider package-page-divider"></li>                        <li class="">
            <a href="http://andrewembler.com/index.php/dashboard/system/liberta/">Liberta</a>
        </li>

        <li class="">
            <a href="http://andrewembler.com/index.php/dashboard/system/calendar/">Calendar</a>
        </li>

        <li class="">
            <a href="http://andrewembler.com/index.php/dashboard/system/api/">API</a>
        </li>


    <li class="">
        <a href="http://andrewembler.com/index.php/dashboard/express/">Express</a>
    </li>

    <li class="">
        <a href="http://andrewembler.com/index.php/dashboard/calendar/">Calendar &amp; Events</a>
    </li>

    </ul>

    <menu class="nav flex-column" v-show="activeTab === 'favorites'">
        <li v-for="item in favorites"><a :href="item.url">{{item.name}}</a></li>
    </menu>

    <div class="ccm-panel-dashboard-footer">
        <p><?= t('Logged in as <a href="%s">%s</a>', URL::to('/account'), $ui->getUserDisplayName()); ?>. </p>
        <a href="<?= URL::to('/login', 'do_logout', Loader::helper('validation/token')->generate('do_logout')); ?>"><?= t('Sign Out.'); ?></a>
    </div>
</section>


<script type="text/javascript">

    $(function () {

        Concrete.Vue.activateContext('cms', function (Vue, config) {
            new Vue({
                el: 'section[data-view=dashboard-panel]',
                components: config.components,
                mounted() {

                },
                methods: {},
                data: {
                    activeTab: 'dashboard',
                    topLevelMenu: <?=json_encode($topLevelMenu)?>,
                    sectionMenu: <?=json_encode($sectionMenu)?>,
                    favorites: <?=json_encode($favorites)?>
                }
            })
        })

    })

</script>
