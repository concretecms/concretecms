<?
defined('C5_EXECUTE') or die("Access Denied.");
$class = 'ccm-content-editor';
$form = Loader::helper('form');
?>

<div class="control-group">
	<label class="control-label"><?=$label?></label>
	<div class="controls">
		<?
		print $form->textarea($this->field('content'), $controller->getContentEditMode(), array(
			'class' => $class,
			'style' => 'width: 580px; height: 380px'
		));
		?>
	</div>
</div>

<script type="text/javascript">
var CCM_EDITOR_SECURITY_TOKEN = "<?=Loader::helper('validation/token')->generate('editor')?>";

$(function() {
	$('.<?=$class?>').redactor({
		'plugins': ['concrete5']
	});
});
</script>