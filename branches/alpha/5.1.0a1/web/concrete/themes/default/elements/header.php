<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/2000/REC-xhtml1-20000126/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
	
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />

<!-- Site Header Content //-->
<style type="text/css">@import "<?=$this->getThemePath()?>/main.css";</style>
<style type="text/css">@import "<?=$this->getThemePath()?>/typography.css";</style>

<? Loader::element('header_required'); ?>

</head>
<body>
			
<div id="page">
	<div id="headerSpacer"></div>
	<div id="header">
		<div id="headerNav">
			<?
			$a = new Area('Header Nav');
			$a->display($c);
			?>
		</div>
		<h1 id="logo"><a href="<?=DIR_REL?>/"><?=SITE?></a></h1>

		<?
		// we use the "is edit mode" check because, in edit mode, the bottom of the area overlaps the item below it, because
		// we're using absolute positioning. So in edit mode we add a bit of space so everything looks nice.
		?>
		<? if ($c->isEditMode()) { ?>
			<div class="spacer" style="min-height: 40px"></div>
		<? } else { ?>
			<div class="spacer"></div>
		<? } ?>

		
		<div id="header-area">
			<div class="divider"></div>
			<div id="header-area-inside">
			<?			
			$ah = new Area('Header');
			$ah->display($c);			
			?>	
			</div>	
			
			<? if ($ah->getTotalBlocksInArea() > 0) { ?>
				<div class="divider"></div>
			<? } ?>
		</div>
	</div>			