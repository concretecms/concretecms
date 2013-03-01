<?
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>

<div class="container">
<div class="row">

<?
	$a = new Area('Main');
	$a->setAreaGridColumnSpan(12);
	$a->display($c);
?>
</div>
</div>

<? $this->inc('elements/footer.php'); ?>