<? defined('C5_EXECUTE') or die("Access Denied."); ?> 

<?
	// grab all tags in use based on the path
	
?>
<div id="ccm-search-block-tag-cloud-<?=$bID?>"></div>

<script type="text/javascript">
var tags = [{tag: "computers", count: 56}, {tag: "mobile" , count :12}];
$("#ccm-search-block-tag-cloud-<?=$bID?>").tagCloud(tags, {
	click: function(tag, event) {
		window.location.href="<?=$this->url($resultTargetURL)?>?tag=" + tag;
	}
});
</script>
