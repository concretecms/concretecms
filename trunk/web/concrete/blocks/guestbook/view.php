<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<? $c = Page::getCurrentPage(); ?><style>

h4.guestBook-title {
	border-bottom:1px solid #666666;
	margin-top:30px;
}

div.guestBook-entry {
	padding:4px 0 4px 0;
	margin:6px 0 12px 0;
}

.guestBook-entry div.contentByLine {
	font-size:.8em;
	color:#333333;
	margin-bottom: 4px;
}

.guestBook-entry div.guestBook-manage-links {
	font-size:.8em;
	color:#333333;
	text-align:right;
}
.guestBook-formBlock {
	margin:12px 0 12px 0;
}
.guestBook-formBlock label {
	width:60px;
	display:block;
	float:left;
}
.guestBook-formBlock textarea {
	width:100%;
	height: 150px;
	margin: 12px 0 12px 0;
}
.guestBook-formBlock .note {
	font-size:10px;
}

.guestBook-formBlock span.error, div#guestBook-formBlock-<?=$controller->bID?> span.error {
	color:#990000;
	text-align:left;
}
</style>
<h4 class="guestBook-title"><?=$controller->title?></h4>
<?php if($invalidIP) { ?>
<div class="ccm-error"><p><?=$invalidIP?></p></div>
<? } ?>
<?
$u = new User();
$posts = $controller->getEntries();
$bp = $controller->getPermissionsObject(); 
foreach($posts as $p) { ?>
	<? if($p['approved'] || $bp->canWrite()) { ?>
    <div class="guestBook-entry">
    	<? if($bp->canWrite()) { ?> 
				<div class="guestBook-manage-links">
                	<a href="<?=$this->action('loadEntry')."&entryID=".$p['entryID'];?>#guestBookForm"><?=t('Edit')?></a> | 
					<a href="<?=$this->action('removeEntry')."&entryID=".$p['entryID'];?>" onclick="return confirm('<?=t("Are you sure you would like to remove this comment?")?>');"><?=t('Remove')?></a> |
                	<? if($p['approved']) { ?>
 	                   	<a href="<?=$this->action('unApproveEntry')."&entryID=".$p['entryID'];?>"><?=t('Un-Approve')?></a>
                    <? } else { ?>
	                    <a href="<?=$this->action('approveEntry')."&entryID=".$p['entryID'];?>"><?=t('Approve')?></a>
					<? } ?>
                </div>
			<? } ?>
			<div class="contentByLine">
				<?=t('Posted by')?>
				<span class="userName">
					<?
					if( intval($p['uID']) ){
						$ui = UserInfo::getByID(intval($p['uID']));
						if (is_object($ui)) {
							echo $ui->getUserName();
						}
					}else echo $p['user_name'];
					?>
				</span> 
				<?=t('on')?>
				<span class="contentDate">
					<?=date("M dS, Y",strtotime($p['entryDate']));?>
				</span>
			</div>
			<?=nl2br($p['commentText'])?>
    </div>
	<? } ?>
<? }

 if (isset($response)) { ?>
	<?=$response?>
<? } ?>
<? if($controller->displayGuestBookForm) { ?>
	<?	
	if( $controller->authenticationRequired && !$u->isLoggedIn() ){ ?>
		<div><?=t('You must be logged in to leave a reply.')?> <a href="<?=View::url("/login","forward",$c->getCollectionID())?>"><?=t('Login')?> &raquo;</a></div>
	<? }else{ ?>	
		<a name="guestBookForm-<?=$controller->bID?>"></a>
		<div id="guestBook-formBlock-<?=$controller->bID?>" class="guestBook-formBlock">
			<h5 class="guestBook-formBlock-title"><?php echo t('Leave a Reply')?></h5>
			<form method="post" action="<?=$this->action('form_save_entry', '#guestBookForm-'.$controller->bID)?>">
			<? if(isset($Entry->entryID)) { ?>
				<input type="hidden" name="entryID" value="<?=$Entry->entryID?>" />
			<? } ?>
			
			<? if(!$controller->authenticationRequired){ ?>
				<label for="name"><?=t('Name')?>:</label><?=(isset($errors['name'])?"<span class=\"error\">".$errors['name']."</span>":"")?><br />
				<input type="text" name="name" value="<?=$Entry->user_name ?>" /> <br />
				<label for="email"><?=t('Email')?>:</label><?=(isset($errors['email'])?"<span class=\"error\">".$errors['email']."</span>":"")?><br />
				<input type="text" name="email" value="<?=$Entry->user_email ?>" /> <span class="note">(<?=t('Your email will not be publicly displayed.')?>)</span> <br />
			<? } ?>
			
			<?=(isset($errors['commentText'])?"<br /><span class=\"error\">".$errors['commentText']."</span>":"")?>
			<textarea name="commentText"><?=$Entry->commentText ?></textarea><br />
			<?
			if($controller->displayCaptcha) {
				
				echo(t('Please type the letters and numbers shown in the image.'));			   
				
				$captcha = Loader::helper('validation/captcha');				
				$captcha->display();
				print '<br/>';
				$captcha->showInput();		

				echo isset($errors['captcha'])?'<span class="error">' . $errors['captcha'] . '</span>':'';
				
			}
			?>
			<br/><br/>
			<input type="submit" name="Post Comment" value="<?=t('Post Comment')?>" class="button"/>
			</form>
		</div>
	<? } ?>
<? } ?>