<?
	defined('C5_EXECUTE') or die("Access Denied.");
?>

<?
$bo = Block::getByID($bOriginalID);
$bp = new Permissions($bo);
if ($bp->canWrite()) {
	$bv = new BlockView(); ?>
	
		<div class="alert-message block-message info" style="margin-bottom: 10px" ><p><?=t("This block was copied from another location. Editing it will create a new instance of it.")?></p></div>

	<?
	
	$bv->render($bo, 'edit', array(
		'c' => $c,
		'a' => $a,
		'proxyBlock' => $b
	));
} else { ?>
	<div class="alert-message error"><?=t("You don't have access to edit the original instance of this block.")?></div>
<? } ?>