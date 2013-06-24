<? defined('C5_EXECUTE') or die("Access Denied."); 
$ownerID = $this->page->vObj->cvAuthorUID;
$u = User::getByUserID($ownerID);
$ownerName = $u->getUserName();
?>
<div class="ccm-gathering-masthead-byline-description">

	<div class="ccm-gathering-tile-title-description">
		<div class="ccm-gathering-tile-headline"><a href="<?=$link?>"><?=$title?></a></div>
		<div class="ccm-gathering-tile-byline"><?php echo t('By '). '<span class="author-name">' .$ownerName. '</span>' ?></div>
		<div class="ccm-gathering-tile-description">
		<?=$description?>
		</div>
	</div>
	<div class="clearfix" style="clear: both;"></div>
</div>
