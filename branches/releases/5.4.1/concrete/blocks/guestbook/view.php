<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php  $c = Page::getCurrentPage(); ?>
<style type="text/css">

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
	float:right;
	padding-left:8px; 
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

.guestBook-formBlock span.error, div#guestBook-formBlock-<?php echo $controller->bID?> span.error {
	color:#990000;
	text-align:left;
}
</style>
<h4 class="guestBook-title"><?php echo $controller->title?></h4>
<?php  if($invalidIP) { ?>
<div class="ccm-error"><p><?php echo $invalidIP?></p></div>
<?php  } ?>
<?php 
$u = new User();
if (!$dateFormat) {
	$dateFormat = t('M jS, Y');
}
$posts = $controller->getEntries();
$bp = $controller->getPermissionsObject(); 
foreach($posts as $p) { ?>
	<?php  if($p['approved'] || $bp->canWrite()) { ?>
    <div class="guestBook-entry">
    	<?php  if($bp->canWrite()) { ?> 
				<div class="guestBook-manage-links">
                	<a href="<?php echo $this->action('loadEntry')."&entryID=".$p['entryID'];?>#guestBookForm"><?php echo t('Edit')?></a> | 
					<a href="<?php echo $this->action('removeEntry')."&entryID=".$p['entryID'];?>" onclick="return confirm('<?php echo t("Are you sure you would like to remove this comment?")?>');"><?php echo t('Remove')?></a> |
                	<?php  if($p['approved']) { ?>
 	                   	<a href="<?php echo $this->action('unApproveEntry')."&entryID=".$p['entryID'];?>"><?php echo t('Un-Approve')?></a>
                    <?php  } else { ?>
	                    <a href="<?php echo $this->action('approveEntry')."&entryID=".$p['entryID'];?>"><?php echo t('Approve')?></a>
					<?php  } ?>
                </div>
			<?php  } ?>
			<div class="contentByLine">
				<?php echo t('Posted by')?>
				<span class="userName">
					<?php 
					if( intval($p['uID']) ){
						$ui = UserInfo::getByID(intval($p['uID']));
						if (is_object($ui)) {
							echo $ui->getUserName();
						}
					}else echo $p['user_name'];
					?>
				</span> 
				<?php echo t('on')?>
				<span class="contentDate">
					<?php echo date($dateFormat,strtotime($p['entryDate']));?>
				</span>
			</div>
			<?php echo nl2br($p['commentText'])?>
    </div>
	<?php  } ?>
<?php  }

 if (isset($response)) { ?>
	<?php echo $response?>
<?php  } ?>
<?php  if($controller->displayGuestBookForm) { ?>
	<?php 	
	if( $controller->authenticationRequired && !$u->isLoggedIn() ){ ?>
		<div><?php echo t('You must be logged in to leave a reply.')?> <a href="<?php echo View::url("/login","forward",$c->getCollectionID())?>"><?php echo t('Login')?> &raquo;</a></div>
	<?php  }else{ ?>	
		<a name="guestBookForm-<?php echo $controller->bID?>"></a>
		<div id="guestBook-formBlock-<?php echo $controller->bID?>" class="guestBook-formBlock">
			<h5 class="guestBook-formBlock-title"><?php  echo t('Leave a Reply')?></h5>
			<form method="post" action="<?php echo $this->action('form_save_entry', '#guestBookForm-'.$controller->bID)?>">
			<?php  if(isset($Entry->entryID)) { ?>
				<input type="hidden" name="entryID" value="<?php echo $Entry->entryID?>" />
			<?php  } ?>
			
			<?php  if(!$controller->authenticationRequired){ ?>
				<label for="name"><?php echo t('Name')?>:</label><?php echo (isset($errors['name'])?"<span class=\"error\">".$errors['name']."</span>":"")?><br />
				<input type="text" name="name" value="<?php echo $Entry->user_name ?>" /> <br />
				<label for="email"><?php echo t('Email')?>:</label><?php echo (isset($errors['email'])?"<span class=\"error\">".$errors['email']."</span>":"")?><br />
				<input type="text" name="email" value="<?php echo $Entry->user_email ?>" /> <span class="note">(<?php echo t('Your email will not be publicly displayed.')?>)</span> <br />
			<?php  } ?>
			
			<?php echo (isset($errors['commentText'])?"<br /><span class=\"error\">".$errors['commentText']."</span>":"")?>
			<textarea name="commentText"><?php echo $Entry->commentText ?></textarea><br />
			<?php 
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
			<input type="submit" name="Post Comment" value="<?php echo t('Post Comment')?>" class="button"/>
			</form>
		</div>
	<?php  } ?>
<?php  } ?>