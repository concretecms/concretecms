<?php

use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;

defined('C5_EXECUTE') or die("Access Denied.");

if (count($messages) > 0) {
    ?>

    <div class="">
        <table class="ccm-search-results-table">
            <thead>
            <tr>
                <th><?= t('ID') ?></th>
                <th><?= t('Error') ?></th>
                <th><?= t('Failed At') ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($messages as $envelope) {
                /**
                 * @var $envelope \Symfony\Component\Messenger\Envelope
                 */

                $stamp = $envelope->last(TransportMessageIdStamp::class);
                $id = null !== $stamp ? $stamp->getId() : null;
                $lastRedeliveryStamp = $envelope->last(RedeliveryStamp::class);
                $lastErrorDetailsStamp = $envelope->last(ErrorDetailsStamp::class);
                $date = null === $lastRedeliveryStamp ? '' : $lastRedeliveryStamp->getRedeliveredAt()->format(
                    'Y-m-d H:i:s'
                );

                $errorMessage = '';
                if (null !== $lastErrorDetailsStamp) {
                    $errorMessage = $lastErrorDetailsStamp->getExceptionMessage();
                } else {
                    foreach (array_reverse($envelope->all(RedeliveryStamp::class)) as $stamp) {
                        if (null !== $stamp->getExceptionMessage()) {
                            $errorMessage = $stamp->getExceptionMessage();
                        }
                    }
                }

                ?>
                <tr data-message-id="<?=$id?>">
                    <td><?= $id ?></td>
                    <td>
                        <div class="ccm-search-results-name"><?= $errorMessage ?></div>
                        <div><?= \get_class($envelope->getMessage()) ?></div>
                    </td>
                    <td><?= $date ?></td>
                    <td>
                        <div class="dropdown" data-menu="search-result">

                            <button class="btn btn-icon"
                                    data-boundary="viewport"
                                    type="button"
                                    data-bs-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false">

                                <svg width="16" height="4">
                                    <use xlink:href="#icon-menu-launcher"/>
                                </svg>
                            </button>

                            <div class="dropdown-menu">
                                <a href="#" data-message-action="retry" class="dropdown-item"><?=t('Retry')?></a>
                                <a href="#" data-message-action="delete" class="dropdown-item"><?=t('Remove')?></a>
                            </div>


                        </div>

                    </td>
                </tr>
                <?php
            } ?>
            </tbody>
        </table>
    </div>

    <script type="text/javascript">
        $(function() {
            $('a[data-message-action]').on('click', function(e) {
                e.preventDefault()
                var $tr = $(this).closest('tr'), id = $tr.data('message-id');
                if ($(this).data('message-action') == 'delete') {
                    var url = <?= json_encode($view->action('delete_message')) ?>;
                    var token = <?= json_encode($token->generate('delete_message')) ?>;
                } else {
                    var url = <?= json_encode($view->action('retry_message')) ?>;
                    var token = <?= json_encode($token->generate('retry_message')) ?>;
                }
                new ConcreteAjaxRequest({
                    url: url,
                    data: {
                        ccm_token: token,
                        id: id
                    },
                    success: function(data) {
                        ConcreteAlert.notify({'message': '<?=t('Message retried.')?>'})
                        $tr.hide('fast', function() {
                            $tr.remove();
                        });
                        if (data && typeof(data.count) !== 'undefined' && data.count == 0) {
                            window.location.reload()
                        }
                    }
                });

            })
        })
    </script>

    <?php
} else { ?>

    <div id="ccm-dashboard-content-regular">
        <p><?= t('There are no failed messages to display.') ?>
    </div>

    <?php
} ?>
