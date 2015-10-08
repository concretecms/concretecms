<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php if (isset($key)) { ?>

<form method="post" action="<?=$view->action('edit')?>" id="ccm-attribute-key-form">

<?php Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type, 'key' => $key)); ?>

</form>


<?php } else if ($this->controller->getTask() == 'select_type' || $this->controller->getTask() == 'add' || $this->controller->getTask() == 'edit') { ?>

	<?php if (isset($type)) { ?>
		<form method="post" action="<?=$view->action('add')?>" id="ccm-attribute-key-form">
	
		<?php Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type)); ?>
	
		</form>	
	<?php } ?>


<?php } else {
	$attribs = FileAttributeKey::getList();
	Loader::element('dashboard/attributes_table', array(
        'types' => $types,
        'category' => $category,
        'attribs'=> $attribs,
        'editURL' => '/dashboard/files/attributes',
        'sortable' => false
        ));
} ?>
