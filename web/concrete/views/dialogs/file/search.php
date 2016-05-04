<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div data-search="files" class="ccm-ui">
	<?php Loader::element('files/search', array('result' => $result))?>
</div>

<script type="text/javascript">
$(function() {
	$('div[data-search=files]').concreteFileManager({
		query: <?=json_encode($query)?>,
		result: <?=json_encode($result->getJSONObject())?>,
		selectMode: 'choose',
		upload_token: '<?=Core::make('token')->generate()?>'
	});
});
</script>