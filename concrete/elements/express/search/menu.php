<?php
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $urlHelper \Concrete\Core\Utility\Service\Url
 */
?>

<div class="row row-cols-auto g-0 align-items-center">

    <?php
    if (!empty($itemsPerPageOptions)) { ?>
        <div class="btn-group">
            <button type="button" class="btn btn-secondary p-2 dropdown-toggle" data-bs-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false"><span
                        id="selected-option"><?= $itemsPerPage; ?></span>
            </button>
            <ul class="dropdown-menu">
                <li class="dropdown-header"><?= t('Items per page') ?></li>
                <?php foreach ($itemsPerPageOptions as $itemsPerPageOption) {
                    $url = $urlHelper->setVariable([
                        'itemsPerPage' => $itemsPerPageOption
                    ]);
                    ?>
                    <li data-items-per-page="<?= $itemsPerPageOption; ?>">
                        <a class="dropdown-item <?= ($itemsPerPageOption === $itemsPerPage) ? 'active' : ''; ?>"
                           href="<?= h($url) ?>"><?= $itemsPerPageOption; ?></a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php }
    ?>

        <ul class="ccm-dashboard-header-icons">
            <?php if ($manageURL) { ?>
                <li><a href="<?= $manageURL ?>" class="ccm-hover-icon launch-tooltip"
                       title="<?= t('Manage Object') ?>"
                    >
                        <i class="fas fa-cog"></i>
                    </a></li>
            <?php } ?>
            <?php if ($exportURL) { ?>
            <li><a href="<?= $exportURL ?>" class="ccm-hover-icon launch-tooltip"
                title="<?= t('Export to CSV') ?>"
                >
                    <i class="fas fa-download"></i>
                </a></li>
            <?php } ?>
            <?php if ($createURL) { ?>
            <li><a href="<?= $createURL ?>" class="ccm-hover-icon launch-tooltip"
                title="<?= t('New %s',
                             $entity->getEntityDisplayName()) ?>"><i class="fas fa-plus"></i></a>
            </li>
            <?php } ?>
        </ul>
</div>
