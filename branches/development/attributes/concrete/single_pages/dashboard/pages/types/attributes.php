<? if (isset($key)) { ?>

<h1><span><?=t('Edit Page Attribute')?></span></h1>
<div class="ccm-dashboard-inner">

<h2><?=t('Attribute Type')?></h2>

<strong><?=$type->getAttributeTypeName()?></strong>
<br/><br/>


<form method="post" action="<?=$this->action('edit')?>" id="ccm-attribute-key-form">

<? Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type, 'key' => $key)); ?>

</form>	

</div>

<? } else { ?>

<h1><span><?=t('Add Page Attribute')?></span></h1>
<div class="ccm-dashboard-inner">

<h2><?=t('Choose Attribute Type')?></h2>

<form method="get" action="<?=$this->action('select_type')?>" id="ccm-attribute-type-form">

<?=$form->select('atID', $types)?>
<?=$form->submit('submit', t('Go'))?>

</form>

<? if (isset($type)) { ?>
	<br/>

	<form method="post" action="<?=$this->action('add')?>" id="ccm-attribute-key-form">

	<? Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type)); ?>

	</form>	
<? } ?>

</div>

<? } ?>