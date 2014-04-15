<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<!DOCTYPE html>
<html lang="<?=Localization::activeLanguage()?>">

<head>

<link rel="stylesheet" href="<?php echo $this->getThemePath(); ?>/css/reset.css" />

<?
$this->addHeaderItem(Loader::helper('html')->css('css/text.css'));
$this->addHeaderItem(Loader::helper('html')->css('css/960_24_col.css'));
$this->addHeaderItem(Loader::helper('html')->css($this->getStyleSheet('main.css')));
$this->addHeaderItem(Loader::helper('html')->css($this->getStyleSheet('typography.css')));
$this->addHeaderItem(Loader::helper('html')->css('//fonts.googleapis.com/css?family=Merriweather:400,700,900,300'));

Loader::element('header_required'); ?>

</head>

<body>

<div class="ccm-page">

<!--start main container -->

<div id="main-container" class="container_24">
	
	<div id="ccm-account-menu-container"></div>

	<div id="main-container-inner">
	
	<div id="header">
	
	
		<?php 
		$a = new GlobalArea('Site Name');
		$a->display();
		?>

		<?php 
		$a = new GlobalArea('Header Nav');
		$a->display();
		?>
		
		<div id="header-image">
		
			<?php 
			$a = new Area('Header Image');
			$a->display($c);
			?>
		
		</div>
		
	</div>
	
	<div class="clear"></div>
