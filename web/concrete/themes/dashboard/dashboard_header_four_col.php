<?
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php', array('enableEditing' => true)); 
?>

<div id="newsflow">
<? $this->inc('elements/header_newsflow.php'); ?>
<table>
<tr>
	<td class="newsflow-em1" colspan="4"><? $a = new Area('Header'); $a->display($c); ?></td>
</tr>
<tr>
	<td style="width: 25%"><? $a = new Area('Column 1'); $a->display($c); ?></td>
	<td style="width: 25%"><? $a = new Area('Column 2'); $a->display($c); ?></td>
	<td style="width: 25%"><? $a = new Area('Column 3'); $a->display($c); ?></td>
	<td style="width: 25%"><? $a = new Area('Column 4'); $a->display($c); ?></td>
</tr>
</table>
</div>

<? $this->inc('elements/footer.php'); ?>