<?
	defined('C5_EXECUTE') or die("Access Denied.");
?>
<div class="ccm-ui">
<?
$bo = Block::getByID($bOriginalID);
$bv = new BlockView(); ?>
	
		<div class="alert-message block-message info" style="margin-bottom: 10px" ><p><?=t("This block was copied from another location. Editing it will create a new instance of it.")?></p></div>

	<?
	
	$bv->render($bo, 'edit', array(
		'c' => $c,
		'a' => $a,
		'proxyBlock' => $b
	));
?>
</div>