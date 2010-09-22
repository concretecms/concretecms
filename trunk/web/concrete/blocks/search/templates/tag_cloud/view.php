<? defined('C5_EXECUTE') or die("Access Denied."); ?> 

<?
	Loader::model('attribute/categories/collection');
	// grab all tags in use based on the path
	$ak = CollectionAttributeKey::getByHandle('tags');
	$akc = $ak->getController();
	$pp = false;
	if ($baseSearchPath != '') {
		$pp = Page::getByPath($baseSearchPath);
	}
	$tags = $akc->getOptionUsageArray($pp);
	$tagString = '';
	for ($i = 0; $i < $tags->count(); $i++) {
		$akct = $tags->get($i);
		$tagString .= "{tag: \"" . $akct->getSelectAttributeOptionValue() . "\", count: " . $akct->getSelectAttributeOptionUsageCount() . "}";
		if (($i + 1) < $tags->count()) {
			$tagString .= ",";			
		}
	}
?>

<div id="ccm-search-block-tag-cloud-<?=$bID?>"></div>

<script type="text/javascript">
var tags = [<?=$tagString?>];
$("#ccm-search-block-tag-cloud-<?=$bID?>").tagCloud(tags, {
	click: function(tag, event) {
		window.location.href="<?=$this->url($resultTargetURL)?>?tag=" + tag;
	}
});
</script>
