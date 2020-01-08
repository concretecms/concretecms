<?php
defined('C5_EXECUTE') or die("Access Denied.");

$this->inc('elements/header_top.php');
?>

    <?php
    $a = new Area('Main');
    $a->enableGridContainer();
    $a->display($c);
    ?>

<?php
$this->inc('elements/footer_bottom.php');
