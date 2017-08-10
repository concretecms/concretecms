<?php
use Concrete\Core\Permission\IPService;

defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Page\View\PageView $view */
/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Controller\SinglePage\Dashboard\System\Permissions\Blacklist\Range $controller */

/* @var int $type */
/* @var Concrete\Core\Permission\IPRange[]|Generator $ranges */
/* @var IPService $ip */

$view->element('dashboard/system/permissions/blacklist/menu', ['type' => $type]);

if (($type & IPService::IPRANGEFLAG_MANUAL) === IPService::IPRANGEFLAG_MANUAL) {
    ?>
    <form class="form-inline" id="ccm-form-new-range">
        <fieldset>
            <legend><?= t('Add IP Range') ?></legend>
            <div class="form-group">
                <label for="new-range" class="launch-tooltip control-label" data-html="true" title="<?= h(t(
                    'Enter a single address<br />(example: %s) or a range<br />(example: %s or %s).<br />Accept both IPv4 and IPv6 ranges.',
                    '<code>1.2.3.4</code>',
                    '<code>1.2.3.*</code>',
                    '<code>1.2.3.0/8</code>'
                )) ?>"><?= t('IP Range') ?></label>
                <input type="text" class="form-control" id="ccm-new-range" required="required" />
            </div>
            <button type="submit" class="btn btn-default"><?= t('Add') ?></button>
            <br />
            <?php
            if (($type & IPService::IPRANGEFLAG_WHITELIST) === IPService::IPRANGEFLAG_WHITELIST) {
                ?>
                <p class="text-muted"><?= t('Your IP address:') ?> <a href="#" onclick="$('#ccm-new-range').val($(this).text());return false"><?= h((string) $ip->getRequestIPAddress()) ?></a></p>
                <?php
            }
            ?>
        </fieldset>
    </form>
    <script>
    $(document).ready(function() {
        function submit(range, force) {
            var send = {
                ccm_token:<?= json_encode($token->generate('add_range/' . $type)) ?>,
                range: range
            };
            if (force) {
                send.force = '1';
            }
            new ConcreteAjaxRequest({
                url: <?= json_encode($view->action('add_range', $type)) ?>,
                data: send,
                success: function(data) {
                    if (data.require_force) {
                        if (window.confirm(data.require_force)) {
                            submit(range, true);
                        }
                        return;
                    }
                    $range = $('#ccm-new-range').val('');
                    $('#ccm-ranges-table>tbody').append(data.row);
                }
            });
               
        }
        $('#ccm-form-new-range').on('submit', function(e) {
            e.preventDefault();
            var $range = $('#ccm-new-range'), range = $.trim($range.val());
            if (range === '') {
                $range.focus();
                return;
            }
            submit(range);
        });
    });
    </script>
    <?php
}
?>
<table class="table table-hover" id="ccm-ranges-table">
    <colgroup>
        <col width="45" />
    </colgroup>
    <thead>
        <tr>
            <th></th>
            <th><?= t('Range') ?></th>
            <?php
            if ($type === IPService::IPRANGETYPE_BLACKLIST_AUTOMATIC) {
                ?>
                <th><?= t('Expires') ?></th>
                <th></th>
                <?php
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($ranges as $range) {
            echo $controller->formatRangeRow($range);
        }
        ?>
    </tbody>
</table>
<script>
$(document).ready(function() {
    $('#ccm-ranges-table>tbody')
        .on('click', 'a.ccm-iprange-delete', function(e) {
            e.preventDefault();
            var $tr = $(this).closest('tr'), id = $tr.data('range-id');
            new ConcreteAjaxRequest({
                url: <?= json_encode($view->action('delete_range', $type)) ?>,
                data: {
                    ccm_token:<?= json_encode($token->generate('delete_range/' . $type)) ?>,
                    id: id
                },
                success: function(data) {
                    $tr.hide('fast', function() {
                        $tr.remove();
                    });
                    if (typeof data === 'string') {
                        window.alert(data);
                    }
                }
            });
        })
        .on('click', 'a.ccm-iprange-makepermanent', function(e) {
            e.preventDefault();
            var $tr = $(this).closest('tr'), id = $tr.data('range-id');
            new ConcreteAjaxRequest({
                url: <?= json_encode($view->action('make_range_permanent', $type)) ?>,
                data: {
                    ccm_token:<?= json_encode($token->generate('make_range_permanent/' . $type)) ?>,
                    id: id
                },
                success: function(data) {
                    $tr.hide('fast', function() {
                        $tr.remove();
                    });
                }
            });

        })
    ;
});
</script>
