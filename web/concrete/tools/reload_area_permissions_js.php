<?
defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getByID($_REQUEST['cID']); 
$a = Area::get($c, $_REQUEST['arHandle']);
$aID = $a->getAreaID();

if (isset($_REQUEST['maximumBlocks'])) {
    $a->getAreaBlocksArray($c); // Needed to calculate the current # of blocks
    $a->setBlockLimit($_REQUEST['maximumBlocks']);
}

Loader::element('block_area_permissions_js', array('a' => $a, 'c' => $c));
echo "$('#ccm-area-menu$aID').remove(); // Remove the old menu to trigger re-generation";
