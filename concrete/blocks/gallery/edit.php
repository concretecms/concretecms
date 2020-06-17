<?php
defined('C5_EXECUTE') or die('Access Denied.');

/** @var Concrete\Core\Form\Service\Form $form */
?>

<div id="ccm-gallery-<?= $bID ?>">
    <gallery-edit :gallery="data" :choices="choices"></gallery-edit>
</div>

<script>
    Concrete.Vue.activateContext('gallery', function(Vue, config) {
        new Vue({
            el: '#ccm-gallery-<?= $bID ?>',
            components: config.components,
            data: function() {
                return {
                    data: JSON.parse(<?= json_encode(json_encode($json)) ?>),
                    choices: JSON.parse(<?= json_encode(json_encode($displayChoices)) ?>),
                }
            }
        })
    })
</script>
