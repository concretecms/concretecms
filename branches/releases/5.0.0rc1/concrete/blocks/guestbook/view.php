<style>

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

.guestBook-formBlock span.error, div#guestBook-formBlock-<?php echo $controller->bID?> span.error {
	color:#990000;
	text-align:left;
}
</style>
<h4 class="guestBook-title"><?php echo $controller->title?></h4>
<?php 
$posts = $controller->getEntries();
$bp = $controller->getPermissionsObject(); 
foreach($posts as $p) { ?>
	<?php  if($p['approved'] || $bp->canWrite()) { ?>
    <div class="guestBook-entry">
    	<?php  if($bp->canWrite()) { ?> 
				<div class="guestBook-manage-links">
                	<a href="<?php echo $this->action('loadEntry')."&entryID=".$p['entryID'];?>#guestBookForm">Edit</a> | 
					<a href="<?php echo $this->action('removeEntry')."&entryID=".$p['entryID'];?>" onclick="return confirm('Are you sure you would like to remove this comment?');">Remove</a> |
                	<?php  if($p['approved']) { ?>
 	                   	<a href="<?php echo $this->action('unApproveEntry')."&entryID=".$p['entryID'];?>">Un-Approve</a>
                    <?php  } else { ?>
	                    <a href="<?php echo $this->action('approveEntry')."&entryID=".$p['entryID'];?>">Approve</a>
					<?php  } ?>
                </div>
			<?php  } ?>
			<div class="contentByLine">Posted by
				<span class="userName"><?php echo $p['user_name']?></span> 
				on
				<span class="contentDate"><?php echo date("M dS, Y",strtotime($p['entryDate']));?></span>
			</div>
			<?php echo nl2br($p['commentText'])?>
    </div>
	<?php  } ?>
<?php  }

 if (isset($response)) { ?>
	<?php echo $response?>
<?php  } ?>
<?php  if($controller->displayGuestBookForm) { ?>
    <a name="guestBookForm-<?php echo $controller->bID?>"></a>
    <div id="guestBook-formBlock-<?php echo $controller->bID?>" class="guestBook-formBlock">
        <h5 class="guestBook-formBlock-title">Leave a Reply</h5>
        <form method="post" action="<?php echo $this->action('form_save_entry', '#guestBookForm-'.$controller->bID)?>">
		<?php  if(isset($Entry->entryID)) { ?>
        	<input type="hidden" name="entryID" value="<?php echo $Entry->entryID?>" />
        <?php  } ?>
        <label for="name">Name:</label><?php echo (isset($errors['name'])?"<span class=\"error\">".$errors['name']."</span>":"")?><br />
		<input type="text" name="name" value="<?php echo $Entry->user_name ?>" /> <br />
        <label for="email">Email:</label><?php echo (isset($errors['email'])?"<span class=\"error\">".$errors['email']."</span>":"")?><br />
		<input type="text" name="email" value="<?php echo $Entry->user_email ?>" /> <span class="note">(your email will not be publicly displayed)</span> <br />
        <?php echo (isset($errors['commentText'])?"<br /><span class=\"error\">".$errors['commentText']."</span>":"")?>
        <textarea name="commentText"><?php echo $Entry->commentText ?></textarea><br />
        <input type="submit" name="Post Comment" value="Post Comment" class="button"/>
        </form>
    </div>
<?php  } ?>