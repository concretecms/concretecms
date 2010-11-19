<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
	
<!-- Site Header Content //-->
<link rel="stylesheet" media="screen" type="text/css" href="<?php echo $this->getStyleSheet('main.css')?>" />
<link rel="stylesheet" media="screen" type="text/css" href="<?php echo $this->getStyleSheet('typography.css')?>" />

<?php  Loader::element('header_required'); ?>

</head>
<body>
<div id="page">
	<div id="headerSpacer"></div>
	<div id="header">
		
		<?php  if ($c->isEditMode()) { ?>
		<div style="min-height: 80px">
		<?php  } ?>
		
		<div id="headerNav">
			<?php 
			$a = new Area('Header Nav');
			$a->display($c);
			?>
		</div>
		
		<h1 id="logo"><!--
			--><a href="<?php echo DIR_REL?>/"><?php 
				$block = Block::getByName('My_Site_Name');  
				if( $block && $block->bID ) $block->display();   
				else echo SITE;
			?></a><!--
		--></h1>

		<?php 
		// we use the "is edit mode" check because, in edit mode, the bottom of the area overlaps the item below it, because
		// we're using absolute positioning. So in edit mode we add a bit of space so everything looks nice.
		?>

		<div class="spacer"></div>

		<?php  if ($c->isEditMode()) { ?>
		</div>
		<?php  } ?>
		
		<div id="header-area">
			<div class="divider"></div>
			<div id="header-area-inside">
			<?php 			
			$ah = new Area('Header');
			$ah->display($c);			
			?>	
			</div>	
			
			<?php  if ($ah->getTotalBlocksInArea() > 0) { ?>
				<div class="divider"></div>
			<?php  } ?>
		</div>
	</div>			