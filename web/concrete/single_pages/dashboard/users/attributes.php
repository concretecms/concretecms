<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? if (isset($key)) { ?>

<form method="post" class="form-horizontal" action="<?=$view->action('edit')?>" id="ccm-attribute-key-form">

<? Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type, 'key' => $key)); ?>

</form>


<? } else if ($this->controller->getTask() == 'select_type' || $this->controller->getTask() == 'add' || $this->controller->getTask() == 'edit') { ?>

	<? if (isset($type)) { ?>
		<form method="post" class="form-horizontal" action="<?=$view->action('add')?>" id="ccm-attribute-key-form">
	
		<? Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type)); ?>
	
		</form>	
	<? } ?>

<? } else { ?>

	<?
	$attribs = UserAttributeKey::getList();
	Loader::element('dashboard/attributes_table', array('types' => $types, 'category' => $category, 'attribs'=> $attribs, 'editURL' => '/dashboard/users/attributes')); ?>

	<? } ?>

<script type="text/javascript">
$(function() {
	$("div.ccm-attributes-list").sortable({
		handle: 'img.ccm-attribute-icon',
		cursor: 'move',
		opacity: 0.5,
		stop: function() {
			var ualist = $(this).sortable('serialize');
			$.post('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/dashboard/user_attributes_update.php', ualist, function(r) {

			});
		}
	});
});

</script>

<style type="text/css">
div.ccm-attributes-list img.ccm-attribute-icon:hover {cursor: move}
</style>