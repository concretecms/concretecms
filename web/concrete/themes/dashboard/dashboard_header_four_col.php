<?php
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php', array('enableEditing' => true)); 
?>

<div class="ccm-ui">
<div class="newsflow" id="newsflow-main">
<?php $this->inc('elements/header_newsflow.php'); ?>
<table class="newsflow-layout">
<tr>
	<td class="newsflow-em1" colspan="4"><?php $a = new Area('Header'); $a->display($c); ?></td>
</tr>
<tr>
	<td class="newsflow-column-quarter"><?php $a = new Area('Column 1'); $a->display($c); ?></td>
	<td class="newsflow-column-quarter"><?php $a = new Area('Column 2'); $a->display($c); ?></td>
	<td class="newsflow-column-quarter"><?php $a = new Area('Column 3'); $a->display($c); ?></td>
	<td class="newsflow-column-quarter"><?php $a = new Area('Column 4'); $a->display($c); ?></td>
</tr>
</table>
</div>
</div>

<?php $this->inc('elements/footer.php'); ?>