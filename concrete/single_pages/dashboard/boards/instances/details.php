<?php
use Concrete\Core\Entity\Board\InstanceLog;

defined('C5_EXECUTE') or die("Access Denied.");

?>

<div class="row">
    <div class="col-4">
        <?php
        $element = Element::get('dashboard/boards/instances/menu', ['instance' => $instance, 'action' => 'details']);
        $element->render();
        ?>
    </div>
    <div class="col-8">

        <h2><?= h($instance->getBoardInstanceName()) ?></h2>

        <hr>

        <form method="post" class="float-end" action="<?=$view->action('refresh_pool', $instance->getBoardInstanceID())?>">
            <?=$token->output('refresh_pool')?>
            <button type="submit" class="btn btn-sm btn-secondary"><?=t("Refresh Data Pool")?></button>
        </form>

        <h4><?=t('Data Source Objects')?></h4>
        <p><?=t('Total data stored in your data pool.')?></p>

        <table class="table table-striped">
            <thead>
            <tr>
                <th></th>
                <th class="w-100"><?=t('Data Source')?></th>
                <th class="text-center"><?=t('#')?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($configuredSources as $configuredSource) {
                $source = $configuredSource->getDataSource();
                $driver = $source->getDriver();
                $formatter = $driver->getIconFormatter();
                $itemCount = $itemRepository->getItemCount($configuredSource, $instance);
                ?>
                <tr>
                    <td><?=$formatter->getListIconElement()?></td>
                    <td><?= h($configuredSource->getName()) ?></td>
                    <td class="text-center"><span class="badge bg-info"><?=$itemCount?></span></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <hr>

        <div>

        <h4 class="mb-4"><?=t('View Instance')?></h4>

        <div class="help-block"><?=t('View the generated board for this instance.')?></div>

            <div class="d-grid">
                <a href="<?=$view->action('view_instance', $instance->getBoardInstanceID())?>"
                   class="btn btn-block btn-secondary"><?=t("View Instance")?></a>
            </div>

        </div>

        <?php if ($instanceLoggingEnabled) { ?>

            <h5 class="my-4"><?=t('Instance Log')?></h5>
            <div class="help-block"><?=t('View details about how content was chosen and placed into the board.')?></div>

            <div id="instance-log">
                <div class="d-grid">
                    <button type="button" class="mb-2 btn-secondary btn btn-outline-secondary" @click="fetchLog"><?=t("Instance Log")?></button>
                </div>

                <div class="modal fade" id="instance-log-modal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?=t('Instance Log')?></h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-info"><?=t('Note: Instance logs are automatically cleared and rebuilt any time the board instance is regenerated.')?></div>
                                <div class="overflow-auto" v-if="logEntries.length">
                                    <table class="w-100 table table-striped table-bordered">
                                        <tbody>
                                            <template v-for="entry in logEntries">
                                            <tr>
                                                <td class="text-nowrap">{{entry.timestampDisplay}}</td>
                                                <td class="w-100">
                                                    <span :class="{'d-inline-block': true, 'me-2': entry.data}">
                                                        {{entry.message}}
                                                    </span>
                                                    <a href="#" v-if="entry.data" class="d-inline-block ccm-hover-icon small" @click.prevent="toggleDetails(entry.id)">
                                                        <?=t('Details')?>
                                                        <i :class="{'fa': true, 'fa-caret-right': !expandedLogEntries.includes(entry.id), 'fa-caret-down': expandedLogEntries.includes(entry.id)}"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr v-show="expandedLogEntries.includes(entry.id)">
                                                <td colspan="2">
                                                    <div style="width: 100%; overflow: hidden">
                                                        <pre><span v-html="entry.displayData"></span></pre>
                                                    </div>
                                                </td>
                                            </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div v-else>
                                    <?=t('No log entries found.')?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <script>
                $(function () {
                    Concrete.Vue.activateContext('backend', function (Vue, config) {
                        new Vue({
                            el: '#instance-log',
                            data() {
                                return {
                                    logEntries: [],
                                    expandedLogEntries: []
                                };
                            },
                            methods: {
                                toggleDetails(entryId) {
                                    if (this.expandedLogEntries.includes(entryId)) {
                                        this.expandedLogEntries.splice(this.expandedLogEntries.indexOf(entryId), 1)
                                    } else {
                                        this.expandedLogEntries.push(entryId)
                                    }
                                },
                                fetchLog() {
                                    const request = new ConcreteAjaxRequest({
                                        url: '<?=$view->action('get_instance_log', $instance->getBoardInstanceID())?>',
                                        method: 'POST',
                                        dataType: 'json',
                                        success: (response) => {
                                            if (response.length) {
                                                this.logEntries = response;
                                            }
                                            const modal = new bootstrap.Modal(document.getElementById('instance-log-modal'));
                                            modal.show();
                                        }
                                    });
                                },
                            },
                        });
                    });
                });
            </script>

        <?php } ?>

        <hr>

        <h4 class="mb-4"><?=t('Update Instance')?></h4>

        <?php if ($instance->isGenerating() && time() - $instance->getDateLastGenerated() < 30) { ?>
            <p><?=t('Board instance generation in progress...')?> <i class="fa fa-spin fa-sync"></i></p>

        <?php } else { ?>
            <div class="container-fluid">
                <form method="post" action="<?=$view->action('refresh_instance', $instance->getBoardInstanceID())?>">
                    <?=$token->output('refresh_instance')?>
                    <div class="row mb-3">
                        <div class="ps-0 col-8 col-offset-1">
                            <h5 class="fw-light"><?=t('Refresh')?></h5>
                            <p><?=t('Refresh the dynamic elements within board slots without getting new items or changing any positioning.')?></p>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn float-end btn-secondary"><?=t("Refresh")?></button>
                        </div>
                    </div>
                </form>
                <form method="post" action="<?=$view->action('regenerate_instance', $instance->getBoardInstanceID())?>">
                    <?=$token->output('regenerate_instance')?>
                    <div class="row mb-1">
                        <div class="ps-0 col-8 col-offset-1">
                            <h5 class="fw-light"><?=t('Regenerate')?></h5>
                            <p><?=t('Regenerate board instance based on current items. Completely removes and rebuilds any board contents.')?></p>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn float-end btn-secondary"><?=t("Regenerate")?></button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col ps-0">
                            <?php if ($instance->getDateLastGenerated() > 0) { ?>
                                <div class="small text-secondary"><?=t('Date Last Generated: %s', $instance->getDateLastGeneratedObject()->format('F d, Y g:i a'))?></div>
                            <?php } ?>
                        </div>
                    </div>
                </form>

            </div>
        <?php } ?>


        <hr>

        <h4 class="mb-4"><?=t('Delete Instance')?></h4>

        <form method="post" action="<?=$view->action('delete_instance', $instance->getBoardInstanceID())?>">
            <?=$token->output('delete_instance')?>
            <div class="d-grid">
                <button type="button"
                        data-bs-toggle="modal" data-bs-target="#delete-instance-<?=$instance->getBoardInstanceID()?>"
                        class="btn btn-block btn-outline-danger"><?=t("Delete Instance")?></button>
            </div>
        </form>

        <div class="modal fade" id="delete-instance-<?=$instance->getBoardInstanceID()?>" tabindex="-1">
            <form method="post" action="<?=$view->action('delete_instance', $instance->getBoardInstanceID())?>">
                <?=$token->output('delete_instance')?>
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?=t('Delete Instance')?></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                        </div>
                        <div class="modal-body">
                            <?=t('Are you sure you want to remove this board instance? If it is referenced on the front-end anywhere that block will be removed.')?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary me-auto" data-bs-dismiss="modal"><?=t('Cancel')?></button>
                            <button type="submit" class="btn btn-danger float-end"><?=t('Delete Instance')?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>


    </div>
</div>
