<?php
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>

<main class="container">
    <div class="col-md-12">
        <?
        $a = new Area('Main');
        $a->display($c);
        ?>
    </div>
</main>

<?php  $this->inc('elements/footer.php'); ?>
