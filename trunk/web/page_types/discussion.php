<div class="discussion">

<?
$nav = Loader::helper('navigation');
foreach($posts as $p) { 
	
	$title = $p->getSubject();
	
	?>

	<h3><a href="<?=$nav->getLinkToCollection($p)?>"><?=$title?></a></h3>
	<div class="discussion-info">
		by
		<span class="discussion-author"><?=$p->getUserName()?></span>
	</div>
</div>
		
		
<? } ?>

<br/><br/>

<? if ($this->controller->getTask() == 'add') { ?>

	<form method="post" action="<?=$this->action('add')?>">
	<div>
	<?= $form->label('subject', 'Topic'); ?>
	<?= $form->text('subject') ?>
	</div>
	
	<div>
	<?= $form->label('message', 'Message'); ?>
	<?= $form->textarea('Message') ?>
	</div>
	
	<?=$form->submit('post', 'Post Message') ?>
	
	</form>
<? } else { ?>
	<a href="<?=$this->action('add')?>">Add Discussion</a>
<? } ?>