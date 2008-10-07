<script src="<?=ASSETS_URL_JAVASCRIPT?>/ccm_spellchecker.js"></script>
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm_spellchecker.css";</style>
<style>
label{float:left; width:25%; display:block}
.fieldWrap{float:left; width:70%}
</style>

<div class="discussion">
	<h2><?=$post->getSubject();?></h2>
	<div class="discussion-info">
		by
		<span class="discussion-author"><?=$post->getUserName()?></span>
	</div>
	<div class="content">
    	<? $a = new Area("Main"); $a->display($c); ?>
    </div>
</div>

<a href="#" onclick="$('#discussion-post-reply-form').toggle(); return false;">Reply</a>
<div id="discussion-post-reply-form" style="display:none;">
<form method="post" action="<?=$this->action('reply')?>">
    <div>
		<?= $form->label('subject', 'Subject'); ?>
		<div class="fieldWrap">
			<?= $form->text('subject',"RE: ".$post->getSubject()) ?>
		</div>
		<div class="spacer"></div>
    </div>
    
    <div>
		<?= $form->label('message', 'Message'); ?>
		<div class="fieldWrap">
			<?= $form->textarea('message') ?>
			<div class="checkSpellingTrigger" style="float:right"><a onClick="SpellChecker.checkField('message',this)">Check Spelling</a></div>
		</div>
		<div class="spacer"></div>
    </div>
    
    <?=$form->submit('post', 'Post Reply') ?>
</form>
</div>
