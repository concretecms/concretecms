<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

<div class="form-group">

    <form method="post" action="<?=$view->action('derp')?>" data-form="choose-items">
        <?=$token->output('derp')?>

        <div v-for="(item, index) in items" :key="index">


            <component :is="item.itemType" v-bind="item.data"></component>

        </div>

        <h3 class="font-weight-light"><?=t('Add Item')?></h3>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown">
                <?=t('Choose Type')?>
            </button>
            <ul class="dropdown-menu">
                <?php
                foreach ($sources as $source) {
                    /**
                     * @var \Concrete\Core\Entity\Board\DataSource $source
                     */
                    $driver = $source->getDriver();
                    $formatter = $driver->getIconFormatter();
                    ?>
                    <li><a class="dropdown-item"><?=$formatter->getListIconElement()?> <?=$source->getName()?></a></li>
                    <?php
                }
                ?>
            </ul>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <button type="submit" class="btn float-right btn-secondary"><?=t('Next')?></button>
            </div>
        </div>
    </form>


</div>

<script type="text/javascript">
$(function() {
    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: 'form[data-form=choose-items]',
            components: config.components,
            data: {
                items: [
                    {
                        'itemType': 'ConcreteEventOccurrenceInput',
                        'data': {
                            'calendarId': 1,
                            'inputName': 'key'
                        }
                    },
                    {
                        'itemType': 'ConcretePageInput',
                        'data': {
                            'inputName': 'key'
                        }
                    }
                ],
            },

            watch: {},

            methods: {}
        })
    })
});
</script>