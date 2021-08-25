<?php
defined('C5_EXECUTE') or die("Access Denied.");

$view->inc('elements/header.php');

?>

    <?php
    $a = new Area('Header');
    $a->enableGridContainer();
    $a->display($c);

    $a = new Area('Main');
    $a->enableGridContainer();
    $a->display($c);
    ?>

<?php
$view->inc('elements/footer.php');
