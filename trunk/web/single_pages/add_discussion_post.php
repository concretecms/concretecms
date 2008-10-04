<? if ($error->has()) { ?>
	<ul class="ccm-error">
	<?
	foreach($error->getList() as $e) { 
		print('<li>' . $e . '</li>');
	}?>
	
	</ul>
	
<? } ?>

<form method="post" action="<?=$this->action('add')?>">
    <div>
    <?= $form->label('subject', 'Subject'); ?>
    <?= $form->text('subject') ?>
    </div>
    
    <div>
    <?= $form->label('message', 'Message'); ?>
    <?= $form->textarea('message') ?>
    </div>
    
    <?=$form->submit('post', 'Post Message') ?>
</form>
