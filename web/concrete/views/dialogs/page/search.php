<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div data-search-pages="<?=$timestamp?>" class="ccm-ui">
<?php Loader::element('pages/search', array('controller' => $searchController))?>
</div>

<script type="text/javascript">
$(function() {
	$('div[data-search-pages=<?=$timestamp?>]').concretePageAjaxSearch({
		result: <?=$result?>,
		mode: 'choose'
	});
});
</script>

<style type="text/css">
	div[data-search=pages].ccm-ui form.ccm-search-fields {
		margin-left: 0px;
	}
</style>
