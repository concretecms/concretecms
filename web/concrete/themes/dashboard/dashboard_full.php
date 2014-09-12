<?
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php', array('enableEditing' => true)); 
?>

<div class="ccm-ui">
<div class="newsflow" id="newsflow-main">
<? $this->inc('elements/header_newsflow.php'); ?>
<? $a = new Area('Main'); $a->display($c); ?>
</div>
</div>

<? $this->inc('elements/footer.php'); ?>