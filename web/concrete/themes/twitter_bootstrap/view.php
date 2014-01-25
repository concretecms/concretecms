<?
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>

<div class="container">
<div class="row">
<div class="span12">

<? 
print $innerContent;
?>

</div>
</div>
</div>

<? $this->inc('elements/footer.php'); ?>