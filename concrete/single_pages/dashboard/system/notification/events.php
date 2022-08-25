<?php
defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php
if ($enable_server_sent_events) {
    ?>
    <form method="post" action="<?=$view->action('submit'); ?>">
        <?php echo $token->output('submit'); ?>

        <div data-view="mercure" class="mb-5" v-cloak>

            <section>
                <h3><?= t('Mercure Server Integration') ?></h3>


                <div class="form-group">
                    <?= $form->label('address', t('Hub Publish URL')) ?>
                    <?= $form->text(
                        'publishUrl',
                        $publishUrl,
                        ['placeholder' => 'https://mercure.my.server.io/.well-known/mercure']
                    ) ?>
                    <div class="help-block"><?= t(
                            'A server that handles subscription requests and distributes the content to subscribers when the corresponding topics have been updated.'
                        ) ?></div>
                </div>

                <div class="form-group">
                    <?= $form->label('connectionMethod', t('Connection Method')) ?>
                    <div class="form-check">
                        <input class="form-check-input" id="connectionMethod1" type="radio" name="connectionMethod"
                               v-model="connectionMethod" value="single_secret_key">
                        <label class="form-check-label" for="connectionMethod1">
                            <?= t('Simple Verification') ?>
                        </label>
                        <div class="mt-2 mb-3 text-muted"><?= (t(
                                'Simple verification uses a single secret for both publisher and subscriber connections, and does not employ public/private keys.'
                            )) ?></div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="connectionMethod" id="connectionMethod2"
                               v-model="connectionMethod" value="rsa_dual">
                        <label class="form-check-label" for="connectionMethod2">
                            <?= t('RSA Public Key') ?>
                        </label>
                        <div class="mt-2 mb-3 text-muted"><?= (t(
                                'RSA Public Key verification uses separate public/private certificates for publishing and subscribing.'
                            )) ?></div>
                    </div>
                </div>

                <div v-if="connectionMethod === 'single_secret_key'">

                    <h5><?=t('Key Settings')?></h5>
                    <div class="form-group">
                        <label class="form-label" for="jwtKey"><?= t('JWT Key') ?></label>
                        <textarea class="form-control" name="jwtKey" v-model="jwtKey" @keydown="isTestConnectionAvailable=false"
                        placeholder="<?=t('Create a key that you specify when starting up Mercure. Make sure it matches the one you save here.')?>"></textarea>
                    </div>

                </div>

                <div v-if="connectionMethod === 'rsa_dual'">

                    <h5><?=t('Keys')?></h5>
                    <div class="form-group">
                        <label class="form-label" for="subscriberPrivateKey"><?= t('Subscriber Private Key') ?></label>
                        <input class="form-control" name="subscriberPrivateKey" v-model="subscriberPrivateKey" @keydown="isTestConnectionAvailable=false"
                                  placeholder="<?=t('/path/to/keys/subscriber.key')?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="publisherPrivateKey"><?= t('Publisher Private Key') ?></label>
                        <input class="form-control" name="publisherPrivateKey" v-model="publisherPrivateKey" @keydown="isTestConnectionAvailable=false"
                               placeholder="<?=t('/path/to/keys/publisher.key')?>">
                    </div>

                </div>


            </section>

            <section class="mb-5">
                <h3><?=t('Authorization Cookie Settings')?></h3>
                <div class="form-group">
                    <?= $form->label('cookieDomain', t('Cookie Domain')) ?>
                    <?= $form->text(
                        'cookieDomain',
                        $cookieDomain,
                        ['placeholder' => t('Leave blank to use the same domain as the website.')]
                    ) ?>
                </div>
                <div class="help-block"><?=t('When authorizing protected updates, authorization access is stored in a `mercureAuthorization` cookie. If your Mercure hub is on a different domain than your site, you will need to ensure your cookie domain is a common domain to both.')?></div>
            </section>

            <section v-if="isTestConnectionAvailable">
                <h3><?= t('Connection Status') ?></h3>

                <div v-if="ping == pong" class="alert alert-success">
                    <?= t('Success! Connection established between front-end and backend Mercure server.') ?>
                </div>

                <div v-if="connectionError" class="alert alert-danger">
                    <div v-if="connectionError === true">
                        <?= t('Mercure server did not respond. Please ensure it is running and properly configured.') ?>
                    </div>
                    <div v-else>
                        {{connectionError}}
                    </div>
                </div>

                <div v-if="!connectionError && pong === null" class="alert alert-secondary"><?= t(
                        'Click the button below to test the connection. The credentials specified above will be used to subscribe to the Mercure Hub, send and receive an update.'
                    ) ?></div>

                <div class="mb-4">
                    <button type="button" class="btn btn-secondary" @click="testConnection"><?= t(
                            'Test Connection'
                        ) ?></button>
                </div>
            </section>



        </div>
        <script>
            $(function () {
                const pingData = '';
                Concrete.Vue.activateContext('cms', function (Vue, config) {
                    new Vue({
                        el: '[data-view=mercure]',
                        data: {
                            ping: '<?=(new \Concrete\Core\Utility\Service\Identifier())->getString(12)?>',
                            pong: null,
                            connectionError: false,
                            connectionMethod: <?=json_encode($connectionMethod)?>,
                            isTestConnectionAvailable: <?= $isTestConnectionAvailable ? 'true' : 'false'?>,
                            jwtKey: '<?=$jwtKey?>',
                            publisherPrivateKey: '<?=$publisherPrivateKey?>',
                            subscriberPrivateKey: '<?=$subscriberPrivateKey?>',
                            eventSourceUrl: '<?=$eventSourceUrl?>',
                            testConnectionTopicUrl: '<?=$testConnectionTopicUrl?>',
                            eventSource: null,
                        },
                        mounted() {
                            var my = this
                        },
                        watch: {
                            connectionMethod: function () {
                                // The moment this changes, we no longer let you test connections until you re-save the page.
                                this.isTestConnectionAvailable = false
                            },
                        },
                        methods: {
                            enableEventSource() {
                                var my = this
                                const eventSourceUrl = new URL(this.eventSourceUrl)
                                eventSourceUrl.searchParams.append('topic', '{+siteUrl}/concrete/events/test_connection')
                                const eventSource = new EventSource(eventSourceUrl, {
                                    withCredentials: true
                                })
                                eventSource.onmessage = event => {
                                    // Will be called every time an update is published by the server
                                    var data = JSON.parse(event.data)
                                    if (data.hasOwnProperty('pong')) {
                                        my.pong = data.pong
                                    }
                                }
                                eventSource.onerror = event => {
                                    my.connectionError = true
                                }
                                my.eventSource = eventSource
                            },
                            testConnection() {
                                var my = this
                                this.enableEventSource()
                                new ConcreteAjaxRequest({
                                    url: '<?=$view->action('test_connection')?>',
                                    data: {
                                        'ping': my.ping
                                    },
                                    method: 'POST',
                                    success: function () {

                                    },
                                    error: function (r) {
                                        my.pong = null
                                        if (typeof r.responseJSON === 'object') {
                                            my.connectionError = r.responseJSON.error.message
                                        } else {
                                            my.connectionError = true
                                        }
                                    },
                                    complete: function() {
                                        if (my.eventSource) {
                                            my.eventSource.close()
                                            $.fn.dialog.hideLoader()
                                        }
                                    }
                                })
                            }
                        }
                    });
                });

            });
        </script>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <button class="btn btn-primary float-end" type="submit" name="save" value="save"><?= t("Save") ?></button>
                <button class="btn btn-danger float-start" type="submit" name="disable" value="1"><?=t('Disable Server-Sent Events')?></button>
            </div>
        </div>
    </form>


<?php
} else { ?>

        <form method="post" action="<?=$view->action('enable_server_sent_events')?>">

            <?php echo $token->output('enable_server_sent_events'); ?>

            <div class="text-center mt-3">
                <p class="lead">
                    <?=t('Server-Sent Events allow a web server to broadcast data in real-time to web clients, like this browser.')?>
                </p>

                <p><?=t('You must enable and run a <a target="_blank" href="https://mercure.rocks/spec">Mercure</a> server in order to subscribe to Server-Sent Events.')?></p>
                <div class="mt-5"><button type="submit" class="btn-primary btn btn-lg"><?=t('Enable Server-Sent Events')?></button></div>
            </div>

        </form>

<?php
} ?>

