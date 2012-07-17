<?
	defined('C5_EXECUTE') or die("Access Denied.");
?>
<?
$bo = Block::getByID($bOriginalID);
$bv = new BlockView(); ?>
	
		<div class="ccm-ui">
			<div class="alert alert-info"><?=t("This block was copied from another location. Editing it will create a new instance of it.")?></div>
		</div>
		
	<?
	
	$bv->render($bo, 'edit', array(
		'c' => $c,
		'a' => $a,
		'proxyBlock' => $b
	));
?>
