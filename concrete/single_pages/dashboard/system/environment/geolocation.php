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
            <thead>
                <tr>
                    <th><?= t('Handle') ?></th>
                    <th><?= t('Display Name') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($geolocators as $geolocator) {
                    $activeFound = $activeFound || $geolocator->isActive(); ?>
                    <tr data-editurl="<?= $this->action('details', $geolocator->getGeolocatorID()) ?>" data-geolocator-id="<?= $geolocator->getGeolocatorID() ?>" class="geolocator <?= $geolocator->isActive() ? 'success' : '' ?>">
                        <td><?= $geolocator->getGeolocatorHandle() ?></td>
                        <td><?= $geolocator->getGeolocatorDisplayName() ?></td>
                    </tr>
                    <?php
                } ?>
            </tbody>
        </table>
    </fieldset>
    <script>
    $(document).ready(function() {
        $('table.geolocation-libraries tr[data-editurl]').on('click', function() {
            window.location.href = $(this).data('editurl');
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
