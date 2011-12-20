<?
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php', array('enableEditing' => true)); 
?>

<div class="ccm-ui">
<div class="newsflow" id="newsflow-main">
<? $this->inc('elements/header_newsflow.php'); ?>
<table class="newsflow-layout">
<tr>
	<td class="newsflow-em1" style="width: 66%" colspan="2" rowspan="2">
	<div id="ccm-dashboard-welcome-back">
	<? $a = new Area('Primary'); $a->display($c); ?>
	</div>
	</td>
	<td><? $a = new Area('Secondary 1'); $a->display($c); ?></td>
</tr>
<tr>
	<td style="width: 34%"><? $a = new Area('Secondary 2'); $a->display($c); ?></td>
</tr>
<tr>
	<td style="width: 33%"><? $a = new Area('Secondary 3'); $a->display($c); ?></td>
	<td style="width: 33%"><? $a = new Area('Secondary 4'); $a->display($c); ?></td>
	<td style="width: 34%"><? $a = new Area('Secondary 5'); $a->display($c); ?></td>
</tr>
</table>
</div>
</div>

<? $this->inc('elements/footer.php'); ?>