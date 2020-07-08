<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

<div class="row">
    <div class="col-4">
        <?php
        $element = Element::get('dashboard/boards/instances/menu', ['instance' => $instance, 'action' => 'rules']);
        $element->render();
        ?>
    </div>
    <div class="col-8 pt-5" data-view="instance-rules">

        <div class="row mb-2">
            <div class="col-4">
                <h4><?=t('Dates')?></h4>
            </div>
            <div class="col-7">
                <h4><?=t('Action')?></h4>
            </div>
        </div>
        <transition-group name="concrete-delete-item">
            <board-instance-rule v-for="(rule, index) in rules" :key="rule.id" v-on:delete="deleteRule(rule, index)" :rule="rule" :show-delete-controls="true"></board-instance-rule>
        </transition-group>

    </div>
</div>

<script type="text/javascript">
    $(function() {
        Concrete.Vue.activateContext('backend', function (Vue, config) {
            new Vue({
                el: 'div[data-view=instance-rules]',
                components: config.components,
                data: {
                    rules: <?=json_encode($rules)?>
                },

                computed: {
                },

                watch: {
                },
                methods: {
                    deleteRule(rule, index) {
                        var my = this
                        new ConcreteAjaxRequest({
                            url: CCM_DISPATCHER_FILENAME + '/ccm/system/board/instance/delete_rule',
                            data: {
                                boardInstanceSlotRuleID: rule.id,
                                ccm_token: CCM_SECURITY_TOKEN,
                            },
                            success: function (r) {
                                my.rules.splice(index, 1);
                            }
                        })
                    }
                }
            })
        })
    });
</script>