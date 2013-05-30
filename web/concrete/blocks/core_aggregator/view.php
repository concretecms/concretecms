<? defined('C5_EXECUTE') or die("Access Denied.");
  Loader::element('aggregator/display', array(
  	'aggregator' => $aggregator,
  	'list' => $itemList
  ));
?>