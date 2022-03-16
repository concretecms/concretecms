<?php
defined('C5_EXECUTE') or die("Access Denied.");
$view->inc('elements/header.php', array('bodyClass' => 'ccm-dashboard-desktop'));
?>

<div class="ccm-dashboard-desktop-content">

    <?php View::element('dashboard/welcome'); ?>

    <div class="ccm-dashboard-desktop-grid <?php if (!$c->isEditMode()) { ?>ccm-dashboard-desktop-flex<?php }  ?>">
        <?php
        $a = new Area('Main');
        $a->setAreaGridMaximumColumns(12);
        $a->display($c);
        ?>
    </div>

    <?php View::element('dashboard/background_image'); ?>

</div>

<?php $view->inc('elements/footer.php'); ?>
