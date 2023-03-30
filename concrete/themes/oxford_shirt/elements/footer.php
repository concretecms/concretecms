<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
use Concrete\Core\Area\GlobalArea;
?>

<footer>
    <section class="mb-4">
        <div class="container">
            <div class="row">
                <div class="col-md-2 col-6">
                    <?php
                    $area = new GlobalArea('Footer Column 2');
                    $area->display();
                    ?>
                </div>
                <div class="col-md-2 col-6">
                    <?php
                    $area = new GlobalArea('Footer Column 3');
                    $area->display();
                    ?>
                </div>
                <div class="col-md-3 ms-auto text-end">
                    <?php
                    $area = new GlobalArea('Footer Column 1');
                    $area->display();
                    ?>
                </div>
            </div>
        </div>
    </section>
    <section class="concrete-branding">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <?=t('Copyright %s. ', date('Y'))?>
                    <?php echo t('Built with <strong><a href="https://www.concretecms.org" title="Concrete CMS" rel="nofollow">Concrete CMS</a></strong>.') ?>
                </div>
                <div class="col-md-6 text-md-end">
                    <?php echo Core::make('helper/navigation')->getLogInOutLink() ?>
                </div>
            </div>
        </div>
    </section>
</footer>


<?php 
$a = new Area('Discussion Popup');
if (($a->getTotalBlocksInArea($c) > 0) || ($c->isEditMode())) {
    $a->display($c);
} else {
    $a = new Area('Discussion Popup');
    $a->display();
}
?>


<?php $view->inc('elements/footer_bottom.php');?>
