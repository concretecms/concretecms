<?
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>

<div class="container">
<div class="row">

<div class="span12">
<?
	$a = new Area('Main');
	$a->setAreaGridMaximumColumns(12);
	$a->display($c);
?>
</div>

</div>

<div class="row">
<div class="span6">
<?
	$a = new Area('Column One');
	$a->setAreaGridMaximumColumns(6);
	$a->display($c);
?>
</div>

<div class="span6">
<?
	$a = new Area('Column Two');
	$a->setAreaGridMaximumColumns(6);
	$a->display($c);
?>
</div>

</div>
</div>

<? $this->inc('elements/footer.php'); ?>