<?php
/**
 * @var $menu \Concrete\Core\Application\UserInterface\Dashboard\Navigation\Navigation
 */
defined('C5_EXECUTE') or die("Access Denied."); ?>

<section data-view="dashboard-panel" v-cloak>
    <menu class="ccm-panel-dashboard-favorites-menu">
        <li :class="{active: activeTab === 'dashboard'}"><a @click="activeTab = 'dashboard'" href="#" id="panel-dashboard-dashboard" data-toggle="pill"><?=t('Dashboard')?></a></li>
        <li :class="{active: activeTab === 'favorites'}"><a @click="activeTab = 'favorites'" href="#" id="panel-dashboard-favorites" data-toggle="pill"><?=t('Favorites')?></a></li>
    </menu>

    <menu class="nav flex-column" v-show="activeTab === 'dashboard'">
        <li v-for="item in menu"><a :href="item.url">{{item.name}}</a></li>
    </menu>

    <menu class="nav flex-column" v-show="activeTab === 'favorites'">
        <li v-for="item in favorites"><a :href="item.url">{{item.name}}</a></li>
    </menu>


    <div class="ccm-panel-dashboard-footer">
        <p><?=t('Logged in as <a href="%s">%s</a>', URL::to('/account'), $ui->getUserDisplayName()); ?>. </p>
        <a href="<?=URL::to('/login', 'do_logout', Loader::helper('validation/token')->generate('do_logout')); ?>"><?=t('Sign Out.'); ?></a>
    </div>
</section>


<script type="text/javascript">

    $(function() {

        Concrete.Vue.activateContext('cms', function (Vue, config) {
            new Vue({
                el: 'section[data-view=dashboard-panel]',
                components: config.components,
                mounted() {

                },
                methods: {

                },
                data: {
                    activeTab: 'dashboard',
                    menu: <?=json_encode($menu)?>,
                    favorites: <?=json_encode($favorites)?>
                }
            })
        })

    })

</script>
