<? defined('C5_EXECUTE') or die("Access Denied.");

if (!isset($editor_selector)) {
	$editor_selector = 'ccm-advanced-editor';
}

$options = "{
	'plugins': ['concrete5']
}";
?>

<script type="text/javascript">
var CCM_EDITOR_SECURITY_TOKEN = "<?=Loader::helper('validation/token')->generate('editor')?>";
$(function() {
	$('.<?=$editor_selector?>').redactor(<?=$options?>);
});
</script>