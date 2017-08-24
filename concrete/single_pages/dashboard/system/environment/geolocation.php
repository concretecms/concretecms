<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Page\Page $c */
/* @var Concrete\Controller\SinglePage\Dashboard\System\Environment\Geolocation $controller */
/* @var Concrete\Core\Application\Service\Dashboard $dashboard */
/* @var Concrete\Core\Error\ErrorList\ErrorList $error */
/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Core\Html\Service\Html $html */
/* @var Concrete\Core\Application\Service\UserInterface $interface */
/* @var Concrete\Theme\Dashboard\PageTheme $theme */
/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Core\Page\View\PageView $view */
/* @var string $pageTitle */

// When vieweing the list:
/* @var Concrete\Core\Entity\Geolocator[] $geolocators */
/* @var IPLib\Address\AddressInterface $ip */

// When vieweing a library
/* @var Concrete\Core\Geolocator\GeolocatorService $service */
/* @var Concrete\Core\Entity\Geolocator $geolocator */
/* @var Concrete\Core\Geolocator\GeolocatorControllerInterface $geolocatorController */

if (isset($geolocator)) {
    ?>
    <form method="POST" action="<?= $this->action('configure', $geolocator->getGeolocatorID()) ?>">
        <?php
        $token->output('ccm-geolocator-configure');
    $description = $geolocator->getGeolocatorDisplayDescription();
    if ($description !== '') {
        ?>
            <div class="alert alert-info">
                <?= $description ?>
            </div>
            <?php
    } ?>
        <div class="form-group">
            <?= $form->label('geolocator-enable', t('Usage')) ?>
            <div class="checkbox">
                <label><?= $form->checkbox('geolocator-active', 1, $geolocator->isActive()) ?> <?= t('Use this geolocator library.') ?></label>
                </div>
            </div>
        </div>
        <?php
        if ($geolocatorController->hasConfigurationForm()) {
            $geolocatorController->renderConfigurationForm($geolocator);
        } ?>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?= $this->action('') ?>" class="btn btn-default pull-left"><?= t('Cancel') ?></a>
                <span class="pull-right">
                    <button type="submit" class="btn btn-primary"><?= t('Save') ?></button>
                </span>
            </div>
        </div>
    </form>
    <?php
} else {
            $activeFound = false; ?>
    <fieldset>
        <table class="table geolocation-libraries">
            <colgroup>
                <col />
                <col />
                <col width="1" />
            </colgroup>
            <thead>
                <tr>
                    <th><?= t('Handle') ?></th>
                    <th><?= t('Display Name') ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($geolocators as $geolocator) {
                    $activeFound = $activeFound || $geolocator->isActive(); ?>
                    <tr
                        data-editurl="<?= $this->action('details', $geolocator->getGeolocatorID()) ?>"
                        data-geolocator-id="<?= $geolocator->getGeolocatorID() ?>"
                        data-geolocator-name="<?= $geolocator->getGeolocatorDisplayName() ?>"
                        class="geolocator <?= $geolocator->isActive() ? 'success' : '' ?>"
                    >
                        <td><?= $geolocator->getGeolocatorHandle() ?></td>
                        <td><?= $geolocator->getGeolocatorDisplayName() ?></td>
                        <td><button class="btn btn-info btn-xs geolocator-test-launcher"><?= t('Test') ?></button></td>
                    </tr>
                    <?php
                } ?>
            </tbody>
        </table>
    </fieldset>
    <div id="ccm-geolocation-test-dialog" class="ccm-ui" style="display: none">
        <div class="form-group">
            <?= $form->label('geolocation-test-ip', t('Test this IP address')) ?>
            <?= $form->text('geolocation-test-ip', (string) $ip) ?>
        </div>
        <div class="alert" style="white-space: pre-wrap"></div>
        <div class="dialog-buttons">
            <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?= t('Close') ?></button>
            <button class="btn btn-primary pull-right geolocation-test-go"><?= t('Test') ?></button>
        </div>
    </div>
    <script>
    $(document).ready(function() {
        $('table.geolocation-libraries tr[data-editurl]').on('click', function() {
            window.location.href = $(this).data('editurl');
        });
        function testGeolocator(geolocatorId, ip, $result) {
            $result
                .removeClass('alert-info alert-danger alert-success')
                .addClass('alert-info')
                .text(<?= json_encode('Loading...') ?>)
                .show();
            $.ajax({
                type: 'POST',
                url: <?= json_encode($view->action('test_geolocator')) ?>,
                data: {
                    <?= json_encode($token::DEFAULT_TOKEN_NAME) ?>: <?= json_encode($token->generate('ccm-geolocator-test')) ?>,
                    geolocatorId: geolocatorId,
                    ip: ip
                },
                dataType: 'json'
            })
            .fail(function (data) {
                var msg;
            	if (data.responseJSON && data.responseJSON.errors) {
                	msg = data.responseJSON.errors.join('\n');
                } else {
                    msg = data.responseText;
                }
                $result.toggleClass('alert-info alert-danger').text(msg);
            })
            .done(function(response) {
                if (response && response.errors) {
                    $result.toggleClass('alert-info alert-danger').text(response.errors.join('\n'));
                    return;
                }
                $result.toggleClass('alert-info alert-success').text(JSON.stringify(response, null, 3));
            });
        }
        $('button.geolocator-test-launcher').on('click', function(e) {
            e.stopPropagation();
            var $tr = $(this).closest('tr'),
            	geolocatorId = $tr.data('geolocator-id'),
            	$dialog = $('#ccm-geolocation-test-dialog'),
                $result = $dialog.find('.alert');
            $result.hide();
            $dialog.find('.geolocation-test-go')
                .off('click')
                .on('click', function() {
                    var ip = $.trim($('#geolocation-test-ip').val());
                    if (ip === '') {
                        $('#geolocation-test-ip').focus();
                        return;
                    }
                    testGeolocator(geolocatorId, ip, $result);
                });
            jQuery.fn.dialog.open({
			    element: $dialog,
                modal: true,
			    width: 320,
			    title: $tr.data('geolocator-name'),
                height: 'auto'
		    });
        });
    });
    </script>
    <?php
    if ($activeFound === false) {
        ?>
        <div class="alert alert-warning">
            <?= t("WARNING: there's no active library.") ?>
        </div>
        <?php
    }
        }
