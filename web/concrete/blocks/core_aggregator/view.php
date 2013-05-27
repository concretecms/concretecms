<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
$c = Page::getCurrentPage();
if ($c->isEditMode()) {
  $bp = new Permissions($b);
  if ($bp->canEditBlock()) { 
    ?>

    <div class="ccm-aggregator-control-bar"></div>

  <? } ?>

<? }

  Loader::element('aggregator/display', array(
  	'aggregator' => $aggregator,
  	'list' => $itemList
  ));
?>