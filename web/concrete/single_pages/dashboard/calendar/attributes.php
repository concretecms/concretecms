<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
use Concrete\Core\Attribute\Key\EventKey;
if (isset($key)) { ?>

<form method="post" action="<?=$view->action('edit')?>" id="ccm-attribute-key-form">

<? Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type, 'key' => $key)); ?>

</form>


<? } else if ($this->controller->getTask() == 'select_type' || $this->controller->getTask() == 'add' || $this->controller->getTask() == 'edit') { ?>

	<? if (isset($type)) { ?>
		<form method="post" action="<?=$view->action('add')?>" id="ccm-attribute-key-form">
	
		<? Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type)); ?>
	
		</form>	
	<? } ?>


<? } else {
	$attribs = EventKey::getList();
	Loader::element('dashboard/attributes_table', array(
        'types' => $types,
        'category' => $category,
        'attribs'=> $attribs,
        'editURL' => '/dashboard/calendar/attributes',
        'sortable' => false
        ));
} ?>