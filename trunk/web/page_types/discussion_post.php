<script src="<?=ASSETS_URL_JAVASCRIPT?>/ccm_spellchecker.js"></script>
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm_spellchecker.css";</style>
<style>
label{float:left; width:25%; display:block}
.fieldWrap{float:left; width:70%}
</style>

<? $av = Loader::helper('concrete/avatar'); ?>

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

<h2>Replies</h2>
<div class="discussion-replies">
<? if (count($replies) > 0) {

	foreach($replies as $r) { ?>
	
	<a name="<?=$r->getCollectionID()?>"></a>
	<table class="discussion-threaded-comment discussion-comment-level-<?=$r->getReplyLevel()?>">
	<tr>
		<td valign="top" class="discussion-threaded-comment-avatar"><?
			$ru = $r->getUserObject();
			print $av->outputUserAvatar($ru);
		?></td>
		<td>
		<h3><?=$r->getSubject()?></h3>
		<?=$r->getBody(); ?>
		<div class="discussion-threaded-comment-nickname">Posted by <strong><?=$r->getUserName()?></strong> on <?=date("M d, Y", strtotime($r->getCollectionDateAdded()))?> at <?=date("g:i A", strtotime($r->getCollectionDateAdded()))?></div>
		</td>
	</tr>
	</table>

<?
	}
	
} else { ?>
	There are no replies to this post.
<? } ?>
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
			<?
			$spellChecker=Loader::helper('spellchecker');
			if($spellChecker->enabled() ){ ?>			
				<div class="checkSpellingTrigger" style="float:right"><a onClick="SpellChecker.checkField('message',this)">Check Spelling</a></div>
			<? } ?>
		</div>
		<div class="spacer"></div>
    </div>
    
    <?=$form->submit('post', 'Post Reply') ?>
</form>
</div>
