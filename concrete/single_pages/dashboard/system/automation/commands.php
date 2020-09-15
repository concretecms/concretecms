<?php
defined('C5_EXECUTE') or die("Access Denied."); ?>

<div data-view="automated-commands" v-cloak>

    <table class="table table-striped" id="ccm-jobs-list">
        <thead>
        <tr>
            <th><?= t('ID') ?></th>
            <th><?= t('Name') ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr :key="command.id" v-for="command in commands">
            <td>{{command.id}}</td>
            <td>{{command.name}}</td>
        </tr>
        </tbody>
    </table>

</div>

<script type="text/javascript">
    $(function () {
        Concrete.Vue.activateContext('backend', function (Vue, config) {
            new Vue({
                el: 'div[data-view=automated-commands]',
                components: config.components,
                data: {
                    commands: <?=json_encode($commands)?>
                },

                computed: {},

                watch: {},
                methods: {}
            })
        })
    });
</script>