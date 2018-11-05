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
        }
        ?>
        <div class="form-group">
            <?= $form->label('geolocator-enable', t('Usage')) ?>
            <div class="checkbox">
                <label><?= $form->checkbox('geolocator-active', 1, $geolocator->isActive()) ?> <?= t('Use this geolocator library.') ?></label>
            </div>
        </div>
        <?php
        if ($geolocatorController->hasConfigurationForm()) {
            $geolocatorController->renderConfigurationForm($geolocator);
        }
        ?>
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
    $activeFound = false;
    ?>
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
        <div class="geotest-processing alert alert-info">
            <?= t('Enter the IP address to be used to test the selected library.') ?>
        </div>
        <div class="geotest-error alert alert-danger hide"></div>
        <div class="geotest-result alert alert-success hide">
            <table class="table table-condensed">
                <tbody>
                    <tr><th><?= t('Has data?') ?></th><td class="georesult-hasData"></td>
                    <tr><th><?= t('Error') ?></th><td class="georesult-error"></td>
                    <tr><th><?= t('City') ?></th><td class="georesult-cityName"></td>
                    <tr><th><?= t('State/Province code') ?></th><td class="georesult-stateProvinceCode"></td>
                    <tr><th><?= t('State/Province name') ?></th><td class="georesult-stateProvinceName"></td>
                    <tr><th><?= t('Postal Code') ?></th><td class="georesult-postalCode"></td>
                    <tr><th><?= t('Country Code') ?></th><td class="georesult-countryCode"></td>
                    <tr><th><?= t('Country Name (in American English)') ?></th><td class="georesult-countryName"></td>
                    <tr><th><?= t('Country Name (in current language)') ?></th><td class="georesult-countryNameLocalized"></td>
                    <tr><th><?= t('Latitude') ?></th><td class="georesult-latitude"></td>
                    <tr><th><?= t('Longitude') ?></th><td class="georesult-longitude"></td>
                </tbody>
            </table>
        </div>
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
        function testGeolocator(geolocatorId, ip, $processing, $error, $result) {
            if (testGeolocator.busy === true) {
                ConcreteAlert.error({
                    message: <?= json_encode(t('The previous request is still waiting for a response.')) ?>
                });
                return;
            }
            testGeolocator.busy = true;
            $error.addClass('hide');
            $result.addClass('hide');
            $processing.text(<?= json_encode(t('Loading...')) ?>).removeClass('hide');
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
                $error.empty().text(msg).removeClass('hide');
            })
            .done(function(response) {
                var value, $out;
                for(var field in response) {
                    value = response[field];
                    $out = $result.find('.georesult-' + field);
                    if ($out.length === 0) {
                        continue;
                    }
                    if (value === null || value === '') {
                        $out.closest('tr').hide();
                        continue;
                    }
                    $out.closest('tr').show();
                    switch (field) {
                        case 'hasData':
                            $out.html(value ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>');
                            break;
                        case 'error':
                            $out.text(<?= json_encode(t('%s (error code: %d)')) ?>.replace(/%d/, value.code.toString()).replace(/%s/, value.message));
                            /*
                            216.58.198.3
                            */
                            break;
                        default:
                            $out.text(value.toString());
                            break;
                    }
                }
                $result.removeClass('hide');
            })
            .always(function() {
                testGeolocator.busy = false;
                $processing.addClass('hide');
            });
        }
        $('button.geolocator-test-launcher').on('click', function(e) {
            e.stopPropagation();
            var $tr = $(this).closest('tr'),
                geolocatorId = $tr.data('geolocator-id'),
                $dialog = $('#ccm-geolocation-test-dialog'),
                $processing = $dialog.find('.geotest-processing'),
                $error = $dialog.find('.geotest-error'),
                $result = $dialog.find('.geotest-result');
            $processing.add($error).add($result)
                .css('min-height', Math.min(Math.max($(window).height() - 350, 100), 300) + 'px')
            $dialog.find('.geolocation-test-go')
                .off('click')
                .on('click', function() {
                    var ip = $.trim($('#geolocation-test-ip').val());
                    if (ip === '') {
                        $('#geolocation-test-ip').focus();
                        return;
                    }
                    testGeolocator(geolocatorId, ip, $processing, $error, $result);
                });
            jQuery.fn.dialog.open({
                element: $dialog,
                modal: true,
                resizable: false,
                width: Math.min(Math.max($(window).width() - 100, 200), 600),
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
