<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Attribute\SocialLinks\Controller $controller
 * @var Concrete\Core\Attribute\View $view
 * @var int $akID
 * @var string[][] $data
 * @var Concrete\Core\Sharing\SocialNetwork\Service[] $services
 */

?>
<div data-attribute-key-id="<?= $akID ?>">
    <div class="ccm-social-link-attribute-wrapper">
        <?php
        foreach ($data['service'] as $i => $serviceHandle) {
            $service = null;
            foreach ($services as $s) {
                if ($s->getHandle() === $serviceHandle) {
                    $service = $s;
                    break;
                }
            }
            if ($service === null) {
                continue;
            }
            ?>
            <div class="ccm-social-link-attribute mb-3">
                <select name="<?= $view->field('service') ?>[]" class="ccm-social-link-service-selector form-select">
                    <?php
                    foreach ($services as $s) {
                        ?>
                        <option value="<?= h($s->getHandle()) ?>" data-icon-html="<?= h($s->getServiceIconHTML()) ?>"<?= $s === $service ? ' selected="selected"' : '' ?>>
                            <?= $s->getDisplayName() ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
                <div class="ccm-social-link-service-text-wrapper input-group input-group-sm">
                    <span class="input-group-text ccm-social-link-attribute-icon"><?= $service->getServiceIconHTML() ?></span>
                    <input name="<?= $view->field('serviceInfo') ?>[]" type="text" value="<?= h($data['serviceInfo'][$i] ?? '') ?>" class="form-control" />
                    <button type="button" class="ccm-social-link-attribute-remove-line btn btn-outline-danger">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <div>
        <button type="button" class="btn btn-sm btn-primary ccm-social-link-attribute-add-service">
            <?= t('Add Link') ?>
            <i class="fas fa-plus"></i>
        </button>
    </div>
</div>
<script>
$(function() {
    ConcreteSocialLinksAttribute.init(<?= $akID ?>);
});
</script>
