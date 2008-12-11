<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<!-- Site Header Content //-->
<style type="text/css">@import "<?php echo $this->getThemePath()?>
/naturalessence.css";</style> 
<?php  Loader::element('header_required'); ?>

	</head>
	<body>
		<div id="wrapper">
			<div id="container">
				<div class="title">
					<h1 id="logo">
						<a href="<?php echo DIR_REL?>/">
<?php echo SITE?>
						</a>
					</h1>
				</div>
				<div class="header">
					<img src="<?=$this->getThemePath()?>/img/header.png" alt="header image" align="middle" />
				</div>
				<div class="navigation">
<?php 
			$an = new Area('Header Nav');
			$an->display($c);
			?>
					<div class="clearer">
					</div>
				</div>
				<div class="main" id="two-columns">
					<div class="col2">
						<div class="left">
							<div class="content">
<?php 
			$a = new Area('Main');
			$a->display($c);
			?>
							</div>
						</div>
						<div class="right">
							<div class="content">
							
<?php 
			$sb = new Area('Sidebar');
			$sb->display($c);
			?>
								
							</div>
						</div>
						<div class="clearer">
						</div>
					</div>
					<div class="bottom">
						<div class="left">
<?php 
			$ab = new Area('Bottom Left');
			$ab->display($c);
			?>
						</div>
						<div class="right">
<?php 
			$aa = new Area('About');
			$aa->display($c);
			?>
						</div>
						<div class="clearer">
						</div>
					</div>
					<div class="footer">
						<div class="left">
							&copy; 
<?php echo date('Y')?>
							<a href="<?php echo DIR_REL?>/">
<?php echo SITE?>
							</a>
							. &nbsp;&nbsp; 
<?php echo t('All rights reserved.')?>
							<span class="sign-in">
								<a href="<?php echo $this->url('/login')?>">
<?php echo t('Sign In to Edit this Site')?>
								</a>
							</span>
						</div>
						<div class="right">
							Design by 
							<a href="http://arcsin.se/">
								Arcsin
							</a>
							<a href="http://templates.arcsin.se/">
								Web Templates
							</a>
						</div>
						<div class="clearer">
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
