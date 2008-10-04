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
    <?= $form->text('subject',"RE: ".$post->getSubject()) ?>
    </div>
    
    <div>
    <?= $form->label('message', 'Message'); ?>
    <?= $form->textarea('message') ?>
    </div>
    
    <?=$form->submit('post', 'Post Reply') ?>
</form>
</div>
