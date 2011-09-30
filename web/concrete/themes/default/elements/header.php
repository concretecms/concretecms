<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="<?=LANGUAGE?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
	
<!-- Site Header Content //-->
<link rel="stylesheet" media="screen" type="text/css" href="<?=$this->getStyleSheet('main.css')?>" />
<link rel="stylesheet" media="screen" type="text/css" href="<?=$this->getStyleSheet('typography.css')?>" />

<? Loader::element('header_required'); ?>

</head>
<body>
<div id="page">
	<div id="headerSpacer"></div>
	<div id="header">
		
		<? if ($c->isEditMode()) { ?>
		<div style="min-height: 80px">
		<? } ?>
		
		<div id="headerNav">
			<?
			$a = new GlobalArea('Header Nav');
			$a->display();
			?>
		</div>
		
		<div style="float: left"><?
		$a = new GlobalArea('Site Heading');
		$a->display();
		?></div>

		<?
		// we use the "is edit mode" check because, in edit mode, the bottom of the area overlaps the item below it, because
		// we're using absolute positioning. So in edit mode we add a bit of space so everything looks nice.
		?>

		<div class="spacer"></div>

		<? if ($c->isEditMode()) { ?>
		</div>
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