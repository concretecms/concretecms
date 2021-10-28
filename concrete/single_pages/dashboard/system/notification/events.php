<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<p class="lead">
    <?=t('Server-Sent Events allow a web server to broadcast data in real-time to web clients, like this browser.')?>
</p>

<p><?=t('You must enable and run a <a target="_blank" href="https://mercure.rocks/spec">Mercure</a> server in order to subscribe to Server-Sent Events.')?></p>

<form method="post" action="<?php echo $view->action('submit'); ?>">
    <?php echo $token->output('submit'); ?>

    <div class="form-check mb-3">
        <?php echo $form->checkbox('enable_server_sent_events', 1, $enable_server_sent_events) ?>
        <label for="enable_server_sent_events"><?php echo t('Enable Server-Sent Events'); ?></label>
    </div>



    <?php if ($enable_server_sent_events) { ?>
        <section data-notification="test-connection">
            <h3><?=t('Mercure Server Integration')?></h3>

            <div class="help-block"><?=t('You must configure a Mercure Hub Server endpoint below. Ensure that when starting the server with the JWT Key automatically created below.')?></div>

            <div class="form-group">
                <?=$form->label('address', t('Hub Publish URL'))?>
                <?=$form->text('publishUrl', $publishUrl)?>
            </div>

            <div class="form-group">
                <label><?=t('JWT Key')?></label>
                <input type="text" class="form-control" readonly onclick="this.select()" value="<?=$jwtKey?>">
            </div>

            <div class="mb-4">
                <button type="button" class="btn btn-block btn-success" @click="testConnection"><?=t('Test Connection')?></button>
            </div>

            <div v-cloak v-if="ping == pong" class="alert alert-success">
                <?=t('Success! Connection established between front-end and backend Mercure server.')?>
            </div>

            <div v-cloak v-if="connectionError" class="alert alert-danger">
                <?=t('Mercure server did not respond. Please ensure it is running and properly configured.')?>
            </div>

        </section>

        <script>
            $(function () {
                const pingData = '';
                Concrete.Vue.activateContext('cms', function (Vue, config) {
                    new Vue({
                        el: '[data-notification=test-connection]',
                        data: {
                            ping: '<?=(new \Concrete\Core\Utility\Service\Identifier())->getString(12)?>',
                            pong: null,
                            connectionError: false
                        },
                        mounted() {
                            var my = this
                            ConcreteEvent.subscribe('ConcreteServerEventTestConnection', function(e, data) {
                                my.pong = data.pong
                            })
                        },
                        methods: {
                            testConnection() {
                                var my = this
                                new ConcreteAjaxRequest({
                                    url: '<?=$view->action('test_connection')?>',
                                    data: {
                                        'ping': my.ping
                                    },
                                    method: 'POST',
                                    success: function() {
                                        my.connectionError = false
                                    },
                                    error: function () {
                                        my.pong = null
                                        my.connectionError = true
                                    }
                                })
                            }
                        }
                    });
                });

            });
        </script>

    <?php } ?>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="btn btn-primary float-end" type="submit"><?=t("Save")?></button>
        </div>
    </div>
</form>
