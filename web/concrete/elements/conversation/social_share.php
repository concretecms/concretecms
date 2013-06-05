<? defined('C5_EXECUTE') or die("Access Denied."); 
$nh = Loader::helper('navigation');
$c = Page::getByID($cID);
$url = $nh->getCollectionURL($c);
$messageID = $message->getConversationMessageID();
?>
<ul class="nav nav-pills cnv-social-share">
	<li class="dropdown">
	<a class="dropdown-toggle" id="drop4" role="button" data-toggle="dropdown" href="#">Share <b class="caret"></b></a>
		<ul id="menu1" class="dropdown-menu" role="menu" aria-labelledby="drop4">
		<li><a class = "shareTweet" target="_blank" href="http://twitter.com/intent/tweet?url=<?php echo $url.'#cnvMessage'.$messageID; ?>"><?php echo t('Twitter'); ?></a></li>
		<li><a class = "shareFacebook" target="_blank" href="http://www.facebook.com/sharer.php?u=<?php echo $url.'#cnvMessage'.$messageID; ?>"><?php echo t('Facebook'); ?></a></li>
		<li><a data-message-id= "<?php echo $messageID ?>" rel="<?php echo $url.'#cnvMessage'.$messageID; ?>" data-dialog-title="<?php echo t('Link') ?>" class="share-permalink" href="#"><?php echo t('Link') ?></a></li>
		</ul>
	</li>
</ul>
