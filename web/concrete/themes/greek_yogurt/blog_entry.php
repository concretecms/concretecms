<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php');
$nav = Loader::helper('navigation');
?>

	<div id="header-image">
		
		<div class="grid_24 alpha omega" id="featured-image-full">
			<?php
			
			if ($c->isEditMode()) {
				print '<br><br>';
				$a = new Area('Thumbnail Image');
				$a->display($c);
			}
			?>
		</div>
		
	</div>
	
	<div class="clear"></div>

	<div id="left-sidebar-container" class="grid_8">

		<div id="left-sidebar-inner">
		
			<?php 
			$a = new Area('Sidebar');
			$a->display($c);
			?>
			
		</div>
	
	</div>

	<div id="main-content-container" class="grid_16">
		<div id="main-content-inner">
		
			<h1><?php echo $c->getCollectionName(); ?></h1>
		
			<?php 
			$a = new Area('Main');
			$a->display($c);
			?>
			
			<div id="main-content-post-author">
			<?php
			$vo = $c->getVersionObject();
			if (is_object($vo)) {
				$uID = $vo->getVersionAuthorUserID();
				$username = $vo->getVersionAuthorUserName();
				if (Config::get("ENABLE_USER_PROFILES")) {
					$profileLink= '<a href="' . $this->url('/profile/view/', $uID) . '">' . $username . '</a>';
				}else{ 
					$profileLink = $username;
				} ?>
				<p>
					<?php echo t(
						/*i18n: %1$s is an author name, 2$s is an URL, %3$s is a date, %4$s is a time */
						'Posted by <span class="post-author">%1$s at <a href="%2$s">%3$s on %4$s</a></span>',
						$profileLink,
						$c->getLinkToCollection,
						$c->getCollectionDatePublic(DATE_APP_GENERIC_T),
						$c->getCollectionDatePublic(DATE_APP_GENERIC_MDY_FULL)
					); ?>
				</p>

				<div id="main-content-post-footer-share">
					<p><?php echo t('Share:'); ?>
					<a href="mailto:?subject=<?php echo $c->getCollectionName(); ?>&body=<?php echo $nav->getLinkToCollection($c, true); ?>"><img class="main-content-post-footer-share-email" src="<?php echo $this->getThemePath(); ?>/images/icon_email.png" alt="<?php echo t('Email'); ?>" /></a>
					<a href="https://twitter.com/share"><img class="main-content-post-footer-share-twitter" src="<?php echo $this->getThemePath(); ?>/images/icon_twitter.png" alt="<?php echo t('Share on Twitter'); ?>" /></a>
					<a href="http://www.facebook.com/share.php?u=<?php echo $nav->getLinkToCollection($c, true); ?>"><img class="main-content-post-footer-share-facebook" src="<?php echo $this->getThemePath(); ?>/images/icon_facebook.png" alt="<?php echo t('Share on Facebook'); ?>" /></a>
					</p>
				</div>
			<? } ?>
			</div>
			
		</div>
	
	</div>
	
<?php if(isset($print) && $print) { ?>
<script type="text/javascript">
$(function(){ window.print(); });
</script>
<?php } ?>
	
	<!-- end main content columns -->
	
<?php $this->inc('elements/footer.php'); ?>
