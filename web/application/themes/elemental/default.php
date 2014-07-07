<?php
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>

<main>
<?
$a = new Area('Main');
$a->setAreaGridColumnSpan(12);
$a->display($c);
?>
</main>

<?php  $this->inc('elements/footer.php'); ?>
