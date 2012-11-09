<?

defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getByID($_REQUEST['cID']);
// add a block to a pile	
$cp = new Permissions($c);
if (!$cp->canViewPage()) {
	exit;
}

$a = Area::get($c, $_REQUEST['arHandle']);
if ($a->isGlobalArea()) {
	$ax = STACKS_AREA_NAME;
	$cx = Stack::getByName($_REQUEST['arHandle']);
} else {
	$cx = $c;
	$ax = $a;
}
$b = Block::getByID($_REQUEST['bID'], $cx, $ax);
if ($b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
	$bi = $b->getInstance();
	$b = Block::getByID($bi->getOriginalBlockID());
}
$bp = new Permissions($b);
if (!$bp->canEditBlock()) {
	exit;
}

$bv = new BlockView();
$bv->render($b, 'inline');

$v = View::getInstance();

$items = $v->getHeaderItems();

if (count($items) > 0) { ?>
<script type="text/javascript">				
<?
foreach($items as $item) { 
	if ($item instanceof CSSOutputObject) { ?>
		// we only support CSS here
		ccm_addHeaderItem("<?=$item->href?>", 'CSS');
	<? } else if ($item instanceof JavaScriptOutputObject) { ?>
		ccm_addHeaderItem("<?=$item->href?>", 'JAVASCRIPT');
	<? } ?>
<? } ?>
</script>
<? }

?>
