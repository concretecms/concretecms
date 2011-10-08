<?
defined('C5_EXECUTE') or die("Access Denied.");
if (!$_GET['external']) {
	$this->inc('elements/header.php', array('enableEditing' => true)); 
?>
<? } ?>

<div id="newsflow">
<? if ($_GET['external']) { ?>
	<ul class="ccm-dashboard-pane-header-icons">
		<li><a href="javascript:void(0)" onclick="ccm_closeNewsflow()" class="ccm-icon-close"><?=t('Close')?></a></li>
	</ul>
<? } ?>

<? $this->inc('elements/header_newsflow_edit.php'); ?>
<table>
<tr>
	<td class="newsflow-em1" style="width: 66%" colspan="2" rowspan="2">
	<? $a = new Area('Primary'); $a->display($c); ?>
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

<? if (!$_GET['external']) { ?>

<? $this->inc('elements/footer.php'); ?>

<? } ?>