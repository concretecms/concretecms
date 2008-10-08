<div class="discussion">
<?
	$bv = new BlockView();
	$bt = BlockType::getByHandle('discussion');
	$controller = Loader::controller($bt);
	$controller->cParentID = $c->getCollectionID();
	$bv->setController($controller);
	$bv->render($bt, 'view');
?>
</div>

<br/>

<a href="<?=$this->action('add')?>">Add Discussion</a>