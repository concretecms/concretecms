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

	$attribs = UserAttributeKey::getList();
	Loader::element('dashboard/attributes_table', array(
        'types' => $types,
        'category' => $category,
        'attribs'=> $attribs,
        'editURL' => '/dashboard/users/attributes',
        'sortable' => true
        ));
} ?>


<script type="text/javascript">
    $(function() {
        $("ul.ccm-sortable-attribute-list-wrapper").sortable({
            handle: 'i.ccm-item-select-list-sort',
            cursor: 'move',
            opacity: 0.5,
            stop: function() {
                var ualist = $(this).sortable('serialize');
                ualist += '&ccm_token=' + '<?=$controller->token->generate('attribute_sort')?>';
                $.post('<?=URL::to('/ccm/system/attribute/attribute_sort/user')?>', ualist, function(r) {});
            }
        });
    });
</script>