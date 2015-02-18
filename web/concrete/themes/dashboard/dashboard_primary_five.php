<?php
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php', array('enableEditing' => true)); 
?>

<div class="ccm-ui">
<div class="newsflow" id="newsflow-main">
<?php $this->inc('elements/header_newsflow.php'); ?>
<table class="newsflow-layout">
<tr>
	<td class="newsflow-em1" style="width: 66%" rowspan="3">
	<div id="ccm-dashboard-welcome-back">
	<?php $a = new Area('Primary'); $a->display($c); ?>
	</div>
	</td>
	<td><?php $a = new Area('Secondary 1'); $a->display($c); ?></td>
</tr>
<tr>
	<td style="width: 34%"><?php $a = new Area('Secondary 2'); $a->display($c); ?></td>
</tr>
<tr>
	<td style="width: 34%"><?php $a = new Area('Secondary 5'); $a->display($c); ?></td>
</tr>
</table>
</div>
</div>

<?php $this->inc('elements/footer.php'); ?>