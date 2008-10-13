<script src="<?=ASSETS_URL_JAVASCRIPT?>/ccm_spellchecker.js"></script>
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm_spellchecker.css";</style>

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
		<div class="discussion-threaded-comment-poster">
			<div class="discussion-threaded-comment-reply"><a href="javascript:void(0)" onclick="ccmDiscussion.reply(<?=$r->getCollectionID()?>)">Reply</a></div>
			Posted by <strong><?=$r->getUserName()?></strong> on <?=date("M d, Y", strtotime($r->getCollectionDateAdded()))?> at <?=date("g:i A", strtotime($r->getCollectionDateAdded()))?>
		
		</div>
		</td>
	</tr>
	</table>

<?
	}
	
} else { ?>
	There are no replies to this post.
<? } ?>
</div>

<div class="discussion-reply">
	<a href="javascript:void(0)" onclick="ccmDiscussion.reply()">Reply to this Post</a>
</div>

<div id="discussion-post-reply-form" style="display:none;">
<form method="post" action="<?=$this->action('reply')?>" target="discussion-frame" onsubmit="ccmDiscussion.submit(this)">
	<?=$form->hidden('cDiscussionPostParentID', '0'); ?>
	<div class="ccm-error" id="discussion-post-errors">
	
	</div>
    <div>
		<?= $form->label('subject', 'Subject'); ?>
		<?= $form->text('subject')?>
    </div>
    
    <div>
		<?= $form->label('message', 'Message'); ?>
		<?= $form->textarea('message') ?>
		<?
		$spellChecker=Loader::helper('spellchecker');
		if($spellChecker->enabled() ){ ?>			
			<div class="checkSpellingTrigger" style="float:right"><a onClick="SpellChecker.checkField('message',this)">Check Spelling</a></div>
		<? } ?>
    </div>
    
    <div>
    	<?= $form->label('attachments', 'Attachments'); ?>
    	<?= $form->file('attachments[]', 'attachments'); ?>
    </div>
    
   <?=$form->submit('post', 'Reply') ?>
   <div class="discussion-post-loader"><img src="<?=ASSETS_URL?>/images/icons/icon_header_loading.gif" /></div>

    <div class="ccm-spacer">&nbsp;</div>
</form>
</div>

<iframe src="" style="display: none" border="0" id="discussion-frame" />
