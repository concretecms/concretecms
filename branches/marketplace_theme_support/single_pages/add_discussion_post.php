<? $spellChecker=Loader::helper('spellchecker'); ?>
<? $spellChecker->init(); ?>


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
		<div class="fieldWrap">
			<?= $form->text('subject') ?>
		</div>
		<div class="spacer"></div>
    </div>
    
    <div>
		<?= $form->label('message', 'Message'); ?>
		<div class="fieldWrap">
			<?= $form->textarea('message') ?>
			<?
			if($spellChecker->enabled() ){ ?>
				<div class="checkSpellingTrigger" style="float:right"><a onClick="SpellChecker.checkField('message',this)">Check Spelling</a></div>
			<? } ?>
		</div>
		<div class="spacer"></div>
    </div>
    
    <?=$form->submit('post', 'Post Message') ?>
</form>
