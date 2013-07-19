<?
defined('C5_EXECUTE') or die("Access Denied.");

$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
	die(t('Access Denied'));
}

$txt = Loader::helper('text');
$args = $_REQUEST;
foreach($args as $key => $value) {
	if (is_array($value)) {
		foreach ($value as $index => $id) {
			$value[$index] = intval($id);
		}
	} else {
		$args[$key] = $txt->entities($value);
	}
}

if (isset($select_mode)) {
	$args['select_mode'] = $select_mode;
}
$args['selectedPageID'] = $_REQUEST['cID'];
if (is_array($args['selectedPageID'])) {
	$args['selectedPageID'] = implode(',',$args['selectedPageID']);
}
$args['sitemapCombinedMode'] = $sitemapCombinedMode;
if (!isset($args['select_mode'])) {
	$args['select_mode'] = 'select_page';
}
if ($args['select_mode'] == 'select_page') {
	$args['reveal'] = $args['selectedPageID'];
}

$args['display_mode'] = 'full';
$args['instance_id'] = time();
Loader::element('dashboard/sitemap', $args);
?>
