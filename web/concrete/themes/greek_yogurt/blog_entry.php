<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>

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
				$u = new User();
				if ($u->isRegistered()) { ?>
					<?php  
					if (Config::get("ENABLE_USER_PROFILES")) {
						$userName = '<a href="' . $this->url('/profile') . '">' . $u->getUserName() . '</a>';
					} else {
						$userName = $u->getUserName();
					}
				}
				?>

				<p><?php echo t('Posted by:');?> <span class="post-author"><?php  echo $userName; ?> at <a href="<?php $c->getLinkToCollection;?>"><?php echo $c->getCollectionDatePublic('g:i a')?> on <?php echo $c->getCollectionDatePublic('F jS, Y')?></a></span></p>
			</div>
			
		</div>
	
	</div>
	
	<!-- end main content columns -->
	
<?php $this->inc('elements/footer.php'); ?>
