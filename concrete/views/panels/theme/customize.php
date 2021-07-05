<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var $skin \Concrete\Core\StyleCustomizer\Skin\SkinInterface
 * @var $styles \Concrete\Core\StyleCustomizer\StyleList
 */
?>

<header>
    <a href="" data-panel-navigation="back" class="ccm-panel-back">
        <svg>
            <use xlink:href="#icon-arrow-left"/>
        </svg>
        <?= t('Customize Skin') ?>
    </a>
</header>

<section data-vue="theme-customizer">

    <theme-customizer :style-list='<?=json_encode($styles)?>'></theme-customizer>

</section>

<script type="text/javascript">
    $(function() {
        Concrete.Vue.activateContext('cms', function (Vue, config) {
            new Vue({
                el: 'section[data-vue=theme-customizer]',
                components: config.components
            })
        })
    })
</script>